<?php
$serv = new swoole_server("0.0.0.0", 9501);
$serv->set(array(
    'worker_num' => 2,
    'task_worker_num' => 2,
));

$serv->on('task', function ($serv, $task_id, $from_id, $data){
});

$serv->on('finish', function ($serv, $task_id, $from_id, $data){
});

$serv->on('receive', function (swoole_server $serv, $fd, $from_id, $data) {
    var_dump($serv->connection_info($fd));
});

$serv->start();