<?php

use Consolidation\Config\ConfigInterface;
use Robo\Config\Config;
use Robo\Contract\ConfigAwareInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RootidCLI implements ConfigAwareInterface {

  public function __construct(Config $config, InputInterface $input, OutputInterface $output) {
    $this->application = new Application('Rootid', '1.x');


  }


  public function setConfig(Config $config)
  {
    // TODO: Implement setConfig() method.
  }

  public function getConfig()
  {
    // TODO: Implement getConfig() method.
  }

}