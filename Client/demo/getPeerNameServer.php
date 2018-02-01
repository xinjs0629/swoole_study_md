<?php
$serv = new swoole_server("127.0.0.1", 9501, SWOOLE_PROCESS, SWOOLE_SOCK_UDP);

$serv->on("packet", function ($server, $data, $client_info){
    var_dump($data);
    var_dump($client_info);
    $server->sendto($client_info['address'], $client_info['port'], "Server ".$data);
});

$serv->start();