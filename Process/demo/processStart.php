<?php
$workers = [];
$worker_num = 3;//创建的进程数

for($i=0;$i<$worker_num ; $i++){
    $process = new swoole_process('process');
    $pid = $process->start();
    $workers[$pid] = $process;
}

foreach($workers as $index => $process){
    echo "index :".$index.PHP_EOL;
    //子进程也会包含此事件
    swoole_event_add($process->pipe, function ($pipe) use($process){
        $data = $process->read();
        echo "RECV: " . $data.PHP_EOL;
    });
}

function process(swoole_process $process){// 第一个处理
    $process->write("进程ID:".$process->pid);
    echo $process->pid,"\t",$process->callback .PHP_EOL;
}
