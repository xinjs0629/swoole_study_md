<?php
$serv = new swoole_server('127.0.0.1', 9501);
$serv->set(array('worker_num' => 4));



$serv->on('connect', function ($serv, $fd){
    echo $serv->setting['worker_num'].PHP_EOL;
});

$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, 'Swoole: '.$data);
    $serv->close($fd);
});
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});
$serv->start();