<?php
$process = new \Swoole\Process(function (\Swoole\Process $childProcess) {
    // 不支持这种写法
    // $childProcess->exec('/usr/local/bin/php /var/www/project/yii-best-practice/cli/yii t/index -m=123 abc xyz');

    // 封装 exec 系统调用
    // 绝对路径
    // 参数必须分开放到数组中
    var_dump('123');
    $childProcess->exec('/usr/local/bin/php', ['1.php', "helloworld\n"]); // exec 系统调用

});
$process->start(); // 启动子进程
