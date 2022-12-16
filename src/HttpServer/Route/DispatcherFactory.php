<?php

namespace Build\HttpServer\Route;

use Build\HttpServer\MiddlewareManger;

class DispatcherFactory
{
    protected array $dispatchers = [];
    protected array $routes = [];
    protected array $routeFiles =[BASE_PATH . '/config/routes.php'];


    public function __construct()
    {
        $this->initConfigRoute();
    }
    public function getDispatcher(string $serverName)
    {
        if(!isset($this->dispatchers[$serverName])) {
            $this->dispatchers[$serverName] = \FastRoute\simpleDispatcher(function (\FastRoute\RouteCollector $r) {
                foreach ($this->routes as $route) {
                    [$httpMethod, $path, $handler] = $route;
                    if(isset($route[3])) {
                        if (isset($route[3]['middleware']) && is_array($route[3]['middleware'])) {
                            MiddlewareManger::addMiddlewares($path, $httpMethod, $route[3]['middleware']);
                        }
                    }
                    $r->addRoute($httpMethod, $path, $handler);
                }
            });
        }

        return $this->dispatchers[$serverName];
    }

    public function initConfigRoute()
    {
        foreach ($this->routeFiles as $file) {
            if (file_exists($file)) {
                $routes = require_once $file;
                $this->routes = array_merge_recursive($this->routes, $routes);
            }
        }
    }

}