<?php

declare(strict_types=1);

use Swoole\HTTP\Server;
use Swoole\HTTP\Request;
use Swoole\HTTP\Response;



$http = new Server('0.0.0.0', 9501);
$http->on('start', static function (Server $server): void {
    echo 'Server started' . \PHP_EOL;
});

$http->on('shutdown', static function (Server $server): void {
    echo 'Server shutdown' . \PHP_EOL;
});

$http->on('request', static function (Request $request, Response $response) use ($http): void {

    $response->detach();
    while (true) {
        if (!$http->exist($response->fd)) {
            break;
        }
        \Swoole\Coroutine::sleep(1);
        $http->send($response->fd, "HTTP/1.1 200 OK\r\nAccess-Control-Allow-Origin: *\r\nContent-Type: text/event-stream\r\nCache-Control: no-cache\r\nConnection: keep-alive\r\nX-Accel-Buffering: no\r\n\r\ndata: " . time() . "\r\nevent: tt\r\n\r\n");
    }
    
});

$http->start();