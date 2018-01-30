<?php
$serv = new swoole_server("0.0.0.0", 9501);

$buffer = array();
$serv->on('receive', function($serv, $fd, $from_id, $data) {
    global $buffer;
    $buffer[$fd] = $data;
    var_dump($buffer);
});

$serv->start();