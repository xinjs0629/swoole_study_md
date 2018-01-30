<?php
$serv = new swoole_server("0.0.0.0", 9501);

$serv->buffer = array();
$serv->on('receive', function($serv, $fd, $from_id, $data){
    $serv->buffer[$fd] = $data;
    var_dump($serv->buffer);
});

$serv->start();