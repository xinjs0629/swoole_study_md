<?php
$serv = new swoole_server("127.0.0.1", 9501);

$serv->set(array(
    'reactor_num' => 2,
    'worker_num' => 4,
    'max_request' => 50,
    'dispatch_mode' => 1,
    'log_file' => '/tmp/swoole.log',
    'daemonize' => 0,
    'task_worker_num' => 2,
));

$serv->on('WorkerStart', function ($serv, $worker_id){
    global $argv;
    if($worker_id >= $serv->setting['worker_num']) {
        echo $worker_id."php {$argv[0]} task worker:".$serv->taskworker.PHP_EOL;
        var_dump($serv->taskworker);
    } else {
        echo $worker_id."php {$argv[0]} event worker:".$serv->taskworker.PHP_EOL;
        var_dump($serv->taskworker);
    }
});

$serv->on('connect', function ($serv, $fd){
    echo "Client:Connect.\n";
});
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    $serv->task("async task coming");
});
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

$serv->on('task', function ($serv, $task_id, $from_id, $data){
});
$serv->on('finish', function ($serv, $fd, $from_id){
});
$serv->start();