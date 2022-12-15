<?php
 namespace Build\Server;

 class Server implements ServerInterface
 {

     protected  $server;

     public function __construct()
     {
     }

     public function init(array $config): ServerInterface
     {
         // TODO: Implement init() method.
         foreach ($config['servers'] as $server) {
            $this->server = new \Swoole\Http\Server($server['host'], $server['port'], $server['type']);
            $this->registerSwooleEvents($server['callbacks']);
         }
         return $this;
     }

     public function start()
     {
         $this->getServer()->start();
     }

     public function getServer()
     {
         return $this->server;
     }

     public function registerSwooleEvents(array $callbacks)
     {
         foreach ($callbacks as $event => $callback) {
             [$class, $method] = $callback;
             $this->server->on($event, [ new $class(), $method]);
         }
     }
 }