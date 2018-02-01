<?php

$client = new swoole_client(SWOOLE_SOCK_UDP, SWOOLE_SOCK_ASYNC); //异步非阻塞

$client->on("connect", function($cli) {
    $cli->send("hello world\n");
});

$client->on("receive", function($cli, $data = ""){
    var_dump($data);
    var_dump($cli->getPeerName());
});

$client->on("close", function($cli){
    //$cli->close(); // 1.6.10+ 不需要
    echo "close\n";
});

$client->on("error", function($cli){
    exit("error\n");
});

$client->connect('127.0.0.1', 9501, 0.5);
