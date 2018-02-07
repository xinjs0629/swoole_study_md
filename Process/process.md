swoole-1.7.2增加了一个进程管理模块，用来替代PHP的pcntl扩展。

需要注意Process进程在系统是非常昂贵的资源，创建进程消耗很大。另外创建的进程过多会导致进程切换开销大幅上升。可以使用`vmstat`指令查看操作系统每秒进程切换的次数。

```shell
vmstat 1 1000
procs -----------memory---------- ---swap-- -----io---- -system-- ------cpu-----
 r  b   swpd   free   buff  cache   si   so    bi    bo   in   cs us sy id wa st
 0  0      0 8250028 509872 4061168    0    0    10    13   88   86  1  0 99  0  0
 0  0      0 8249532 509872 4061936    0    0     0     0  451 1108  0  0 100  0  0
 0  0      0 8249532 509872 4061884    0    0     0     0  684 1855  1  3 95  0  0
 0  0      0 8249532 509880 4061876    0    0     0    16  492 1332  0  0 99  0  0
 0  0      0 8249532 509880 4061844    0    0     0     0  379  893  0  0 100  0  0
 0  0      0 8249532 509880 4061844    0    0     0     0  440 1116  0  0 99  0  0
```

PHP自带的pcntl，存在很多不足，如
----
* pcntl没有提供进程间通信的功能
* pcntl不支持重定向标准输入和输出
* pcntl只提供了fork这样原始的接口，容易使用错误
* swoole_process提供了比pcntl更强大的功能，更易用的API，使PHP在多进程编程方面更加轻松。

swoole_process提供了如下特性：
----
* swoole_process提供了基于unixsock的进程间通信，使用很简单只需调用write/read或者push/pop即可
* swoole_process支持重定向标准输入和输出，在子进程内echo不会打印屏幕，而是写入管道，读键盘输入可以重定向为管道读取数据
* 配合swoole_event模块，创建的PHP子进程可以异步的事件驱动模式
* swoole_process提供了exec接口，创建的进程可以执行其他程序，与原PHP父进程之间可以方便的通信

一个同步实例:
----
* 子进程异常退出时,自动重启
* 主进程异常退出时,子进程在干完手头活后退出
```php
(new class{
    public $mpid=0;
    public $works=[];
    public $max_precess=1;
    public $new_index=0;

    public function __construct(){
        try {
            swoole_set_process_name(sprintf('php-ps:%s', 'master'));
            $this->mpid = posix_getpid();
            $this->run();
            $this->processWait();
        }catch (\Exception $e){
            die('ALL ERROR: '.$e->getMessage());
        }
    }

    public function run(){
        for ($i=0; $i < $this->max_precess; $i++) {
            $this->CreateProcess();
        }
    }

    public function CreateProcess($index=null){
        $process = new swoole_process(function(swoole_process $worker)use($index){
            if(is_null($index)){
                $index=$this->new_index;
                $this->new_index++;
            }
            swoole_set_process_name(sprintf('php-ps:%s',$index));
            for ($j = 0; $j < 16000; $j++) {
                $this->checkMpid($worker);
                echo "msg: {$j}\n";
                sleep(1);
            }
        }, false, false);
        $pid=$process->start();
        $this->works[$index]=$pid;
        return $pid;
    }
    public function checkMpid(&$worker){
        if(!swoole_process::kill($this->mpid,0)){
            $worker->exit();
            // 这句提示,实际是看不到的.需要写到日志中
            echo "Master process exited, I [{$worker['pid']}] also quit\n";
        }
    }

    public function rebootProcess($ret){
        $pid=$ret['pid'];
        $index=array_search($pid, $this->works);
        if($index!==false){
            $index=intval($index);
            $new_pid=$this->CreateProcess($index);
            echo "rebootProcess: {$index}={$new_pid} Done\n";
            return;
        }
        throw new \Exception('rebootProcess Error: no pid');
    }

    public function processWait(){
        while(1) {
            if(count($this->works)){
                $ret = swoole_process::wait();
                if ($ret) {
                    $this->rebootProcess($ret);
                }
            }else{
                break;
            }
        }
    }
});
```

# swoole_process::kill
向指定pid进程发送信号
```php
bool swoole_process::kill($pid, $signo = SIGTERM);
```
* 默认的信号为SIGTERM，表示终止进程
* $signo=0，可以检测进程是否存在，不会发送信号

僵尸进程
----
子进程退出后，父进程务必要执行swoole_process::wait进行回收，否则这个子进程就会变为僵尸进程。会浪费操作系统的进程资源。

父进程可以设置监听SIGCHLD信号，收到信号后执行swoole_process::wait回收退出的子进程。

# swoole_process::wait

回收结束运行的子进程。

```php
array swoole_process::wait(bool $blocking = true);
$result = array('code' => 0, 'pid' => 15001, 'signal' => 15);
```

* $blocking 参数可以指定是否阻塞等待，默认为阻塞
* 操作成功会返回返回一个数组包含子进程的PID、退出状态码、被哪种信号KILL
* 失败返回false

> 子进程结束必须要执行wait进行回收，否则子进程会变成僵尸进程

> $blocking 仅在1.7.10以上版本可用

>使用swoole_process作为监控父进程，创建管理子process时，父类必须注册信号SIGCHLD对退出的进程执行wait，否则子process一旦被kill会引起父process exit

在异步信号回调中执行wait
-----
```php
swoole_process::signal(SIGCHLD, function($sig) {
  //必须为false，非阻塞模式
  while($ret =  swoole_process::wait(false)) {
      echo "PID={$ret['pid']}\n";
  }
});
```
* 信号发生时可能同时有多个子进程退出
* 必须循环执行wait直到返回false



