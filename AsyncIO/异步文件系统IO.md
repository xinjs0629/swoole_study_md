# 异步文件系统IO

Swoole支持2种类型的异步文件读写IO，可以使用`swoole_async_set`来设置AIO模式:.

Linux原生异步IO (AIO模式：SWOOLE_AIO_LINUX)
-----
基于Linux Native AIO系统调用，是真正的异步IO，并非阻塞模拟。

__优点：__

* 所有操作均在一个线程内完成，不需要开线程池
* 不依赖线程执行IO，所以并发可以非常大

__缺点：__

* 只支持DriectIO，无法利用PageCache，所有对文件读写都会直接操作磁盘
* 写入数据的size必须为`512`整数倍数
* 写入数据的offset必须为`512`整数倍数


线程池模式异步IO (AIO模式： SWOOLE_AIO_BASE)
-----
基于线程池模拟实现，文件读写请求投递到任务队列，然后由AIO线程读写文件，完成后通知主线程。AIO线程本身是同步阻塞的。所以并非真正的异步IO。

__优点：__

* 可以利用操作系统PageCache，读写热数据性能非常高，等于读内存

> 可修改`thread_num`项设置启用的AIO线程数量

__缺点：__

* 并发较差，不支持同时读写大量文件，最大并发受限与AIO的线程数量

# swoole_async_readfile

异步读取文件内容，函数原型
```php
//函数风格
swoole_async_readfile(string $filename, mixed $callback);
//命名空间风格
Swoole\Async::readFile(string $filename, mixed $callback);
```
* 文件不存在会返回`false`
* 成功打开文件立即返回`true`
* 数据读取完毕后会回调指定的`callback`函数。


使用示例：
----------
```php
swoole_async_readfile(__DIR__."/server.php", function($filename, $content) {
     echo "$filename: $content";
});
```

> `swoole_async_readfile`会将文件内容全部复制到内存，所以不能用于大文件的读取
> 如果要读取超大文件，请使用`swoole_async_read`函数
> `swoole_async_readfile`最大可读取`4M`的文件，受限于`SW_AIO_MAX_FILESIZE`宏