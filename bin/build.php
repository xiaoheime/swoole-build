<?php

use Build\Command\StartCmd;
use Symfony\Component\Console\Application;

require "vendor/autoload.php";

! defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));

$application = new Application();

$config  = (new \Build\Config\ConfigFactory())();

foreach ($config->get('commands') as $key=>$command) {
    if ($command === StartCmd::class) {
        $application->add(new $command($config));
    }else{
        $application->add(new $command);

    }
}

$application->run();
