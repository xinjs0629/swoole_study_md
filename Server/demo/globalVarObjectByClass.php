<?php
class Server
{
    public $buffer;
    public $serv;

    function onReceive($serv, $fd, $from_id, $data)
    {
        //在这里可以读取到EventCallback对象上的属性和方法
        $this->buffer[$fd] = $data;
        $this->hello();
    }

    function hello()
    {
        var_dump($this->buffer);
    }

    function run()
    {
        $serv = new swoole_server('127.0.0.1', 9501);
        $this->serv  = $serv;
        $serv->on('receive', array($this, 'onReceive'));
        $serv->start();
    }
}

$server= new Server;
$server->run();