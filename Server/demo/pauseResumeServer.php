<?php
$serv = new swoole_server("0.0.0.0", 9501, SWOOLE_BASE);
$serv->set(array(
    'worker_num' => 2,
    'task_worker_num' => 2,
));

$serv->on('task', function ($serv, $task_id, $from_id, $data){
});

$serv->on('finish', function ($serv, $task_id, $from_id, $data){
});

$serv->on('receive', function (swoole_server $serv, $fd, $from_id, $data) {
    echo "fd:".$fd."--".$data.PHP_EOL;
    var_dump($fd);
    $serv->pause($fd);
    sleep(5);
    $serv->resume($fd);
});

$serv->start();