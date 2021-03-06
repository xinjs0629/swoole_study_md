<?php
$serv = new swoole_server("127.0.0.1", 9501);

$serv->set(array(
    'reactor_num' => 2,
    'worker_num' => 4,
    'max_request' => 50,
));

$serv->on("workerstart", function ($serv, $work_id){
    $client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);
    $client->connect('127.0.0.1', 9501);
    $client->send("swoole:".$work_id);
});

$serv->on('connect', function ($serv, $fd){
    echo "Client:Connect.\n";

});
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    var_dump("server revice:".$data);
    //$serv->send($fd, 'Swoole: '.$data);
    $serv->close($fd);
});
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});
$serv->start();