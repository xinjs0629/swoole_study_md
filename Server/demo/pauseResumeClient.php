<?php
$client = new swoole_client(SWOOLE_SOCK_TCP);

//连接到服务器
if (!$client->connect('127.0.0.1', 9501, 0.5)) {
    die("connect failed.");
}
//向服务器发送数据

$id = 0;
/*while (true){
    if($id > 20){
        break;
    }
    $client->send("hello world-".time());
    sleep(1);
    $id++;
}*/
if (!$client->send("hello world")) {
    die("send failed.");
}
//关闭连接
$client->close();