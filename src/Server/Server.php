<?php
 namespace Build\Server;

 use Build\HttpServer\HttpServer;
 use Build\HttpServer\Route\DispatcherFactory;

 class Server implements ServerInterface
 {

     protected \Swoole\Http\Server $server;

     public function __construct()
     {
     }

     public function init(array $config): ServerInterface
     {
         // TODO: Implement init() method.
         foreach ($config['servers'] as $server) {
            $this->server = new \Swoole\Http\Server($server['host'], $server['port'], $server['type']);
            $this->registerEvents($server['callbacks']);
         }
         return $this;
     }

     public function start()
     {
         $this->getServer()->start();
     }

     public function getServer(): \Swoole\Http\Server
     {
         return $this->server;
     }

     public function registerEvents(array $callbacks)
     {
         foreach ($callbacks as $event => $callback) {
             [$class, $method] = $callback;
             if($class === HttpServer::class) {
                 $instance = new $class(new DispatcherFactory());
             }else{
                 $instance = new $class();
             }
             $this->server->on($event, [ $instance, $method]);
             if(method_exists($instance, 'initCoreMiddleware')) {
                 $instance->initCoreMiddleware();
             }
         }
     }
 }