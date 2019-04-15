<?php

namespace RootidCLI;

use Consolidation\Config\ConfigInterface;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Robo\Config\Config;
use Robo\Common\ConfigAwareTrait;
use Robo\Contract\ConfigAwareInterface;
use Robo\Robo;
use Robo\Runner as RoboRunner;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Rootid implements ConfigAwareInterface, ContainerAwareInterface {
  use ContainerAwareTrait;
  use ConfigAwareTrait;
  private $runner;

  public function __construct(Config $config, InputInterface $input = null, OutputInterface $output = null) {
    $this->setConfig($config);

    $application = new Application('Rootid', '0.1');
    $container = Robo::createDefaultContainer($input, $output, $application, $config);
    $this->setContainer($container);
    //$this->addDefaultArgumentsAndOptions($application);
    //$this->configureContainer();
    $this->loadCommands();

    $this->runner = new RoboRunner();
    $this->runner->setContainer($container);
  }  

  public function run(InputInterface $input, OutputInterface $output) {
    $config = $this->getConfig();
    $status_code = $this->runner->run($input, $output, null, $this->commands);

    return $status_code;
  }

  private function loadCommands() {
    $this->commands = [
      'RootidCLI\\Commands\\Sync\\SyncSiteData',
      'RootidCLI\\Commands\\Site\\SiteImport',
      'RootidCLI\\Commands\\Test\\RunSiteTests',
      'RootidCLI\\Commands\\Site\\SiteUpdate'
    ];
  }
}