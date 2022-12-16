<?php

use App\Controller\HelloController;
use App\Middleware\MiddlewareC;

return [
    ['GET', '/hello/index', [HelloController::class, 'index'], [
        'middleware' => [MiddlewareC::class]
    ]],
    ['GET', '/hello/test', [HelloController::class, 'test']]
];
