<?php
$workers = [];
$worker_num = 3;//创建的进程数

for($i=0;$i<$worker_num ; $i++){
    $process = new swoole_process('process');
    $pid = $process->start();
    $workers[$pid] = $process;
}

function process(swoole_process $process){// 第一个处理
    $process->write("进程ID:".$process->pid);
    echo $process->pid,"\t",$process->callback .PHP_EOL;
    sleep(20);
}
