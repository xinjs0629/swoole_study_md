<?php
$workers = [];
$worker_num = 2;

for($i = 0; $i < $worker_num; $i++)
{
    $process = new swoole_process('callback_function', false, false);
    $process->useQueue();
    $pid = $process->start();
    $workers[$pid] = $process;
    //echo "Master: new worker, PID=".$pid."\n";
}

function callback_function(swoole_process $worker)
{
    //echo "Worker: start. PID=".$worker->pid."\n";
    //recv data from master
    var_dump($worker->statQueue());
    $recv = $worker->pop();

    echo "From Master: $recv\n";

    var_dump($worker->statQueue());
    sleep(2);
    $worker->exit(0);
}

foreach($workers as $pid => $process)
{
    $process->push("hello worker[$pid]\n");
}

for($i = 0; $i < $worker_num; $i++)
{
    $ret = swoole_process::wait();
    var_dump($ret);
    $pid = $ret['pid'];
    unset($workers[$pid]);
    echo "Worker Exit, PID=".$pid.PHP_EOL;
}

