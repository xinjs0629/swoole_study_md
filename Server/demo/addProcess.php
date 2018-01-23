<?php
/**
 * Created by PhpStorm.
 * User: asonsun
 * Date: 18/1/23
 * Time: 下午8:55
 */
$server = new swoole_server('127.0.0.1', 9501);

$process = new swoole_process(function($process) use ($server) {

     (true) {
        $msg = $process->read();
        foreach($server->connections as $conn) {
            echo PID
            echo "this is process".PHP_EOL;
            echo $msg.PHP_EOL;
            $server->send($conn, $msg);
        }
    }
});

$server->addProcess($process);

$server->on('receive', function ($serv, $fd, $from_id, $data) use ($process) {
    //群发收到的消息
    echo "this is receive cli".PHP_EOL;
    var_dump($data);
    $process->write($data);
});

$server->start();