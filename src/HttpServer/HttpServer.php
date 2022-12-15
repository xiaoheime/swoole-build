<?php

namespace Build\HttpServer;

class HttpServer
{
    public function onRequest($request, $response)
    {
        $response->header('Content-Type', 'text/html; charset=utf-8');
        $response->end('<h1>Hello Swoole. #' . rand(1000, 9999) . '</h1>');
    }
}