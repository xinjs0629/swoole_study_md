<?php
$serv = new swoole_server("127.0.0.1", 9501);

$serv->set(array(
    'reactor_num' => 2,
    'worker_num' => 4,
    'max_request' => 50,
));

$serv->on('connect', function ($serv, $fd){
    echo "Client:Connect.\n";

});
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    var_dump("server revice:".$data);
});
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});
$serv->start();