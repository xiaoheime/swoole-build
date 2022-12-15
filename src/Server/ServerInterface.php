<?php
namespace Build\Server;
interface ServerInterface {

    public function __construct();

    public function init(array $config): ServerInterface;

    public function start();

    public function getServer();
}
