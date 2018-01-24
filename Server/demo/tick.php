<?php
$serv = new swoole_server("127.0.0.1", 9501);

$serv->set(array(
    'reactor_num' => 2,
    'worker_num' => 4,
    'max_request' => 50,
    'dispatch_mode' => 1,
    'daemonize' => 0,
));

$serv->on('connect', function ($serv, $fd){
    echo "Client:Connect.\n";
});

$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, 'Swoole: '.$data);
    $serv->close($fd);
});

$serv->on("workerstart", function ($serv, $work_id){
    if (!$serv->taskworker) {
        $serv->tick(1000, function() use ($serv) {
            if($serv->worker_id == 0){
                var_dump($serv->worker_id."--helloword"."---".time());
            }
        });
    }
    else
    {
        var_dump('addTimer');
        $serv->addtimer(1000);
    }
});

$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

$serv->start();