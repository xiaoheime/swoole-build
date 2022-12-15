<?php

namespace Build\Command;

use Build\Config\Config;
use Build\Server\ServerFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartCmd extends Command
{
    /**
     * @var Config
     */
    protected Config $config;

    public function __construct(Config $config)
    {
        parent::__construct();
       $this->config = $config;
    }

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            // 命令的名字（"bin/console" 后面的部分）
            ->setName('start')
            ->setDescription('启动服务')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $serverFactory = new ServerFactory();
        $serverFactory->configure($this->config->get('server'));
        $serverFactory->getServer()->start();

       return 1;
    }
}