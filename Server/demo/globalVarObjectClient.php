<?php
$client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);
$client->connect('127.0.0.1', 9501);

$client->send("swoole:".time());
if(time() % 2 == 1){
    $client->send("swoole more:".time());
}
