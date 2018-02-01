<?php

$client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC); //异步非阻塞

$client->on("connect", function($cli) {
    echo "onConnect:".PHP_EOL;
    var_dump($cli->isConnected());
    $cli->send("hello world\n");
});

$client->on("receive", function($cli, $data = ""){
    $data = $cli->recv(); //1.6.10+ 不需要
    if(empty($data)){
        $cli->close();
        echo "i have no data\n";
    } else {
        echo "received: $data\n";
        sleep(1);
        $cli->send("hello\n");
    }
});

$client->on("close", function($cli){
    //$cli->close(); // 1.6.10+ 不需要
    echo "close\n";
});

$client->on("error", function($cli){
    exit("error\n");
});

$client->connect('127.0.0.1', 9501, 0.5);
echo "after connect:".PHP_EOL;
var_dump($client->isConnected());
