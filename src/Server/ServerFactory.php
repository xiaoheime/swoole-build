<?php

namespace Build\Server;

class ServerFactory
{
    protected array $serverConfig = [];
    protected Server $server;

    public function configure(array $configs)
    {
        $this->serverConfig = $configs;
        $this->getServer()->init($this->serverConfig);
    }

    public function getServer(): Server
    {
        if(!isset($this->server)) {
            $this->server = new Server();
        }

        return $this->server;
    }
}