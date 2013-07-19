<?php

namespace PhpSoapClient;

use Symfony\Component\Console\Application as BaseApplication;


class Application extends BaseApplication
{
  public function __construct()
  {
    parent::__construct('php-soap-client', '1.3.0');
  }

  protected function getDefaultCommands()
  {
    $commands = parent::getDefaultCommands();
    $commands[] = new \PhpSoapClient\Command\GetWsdlCommand();
    $commands[] = new \PhpSoapClient\Command\CallMethodCommand();
    $commands[] = new \PhpSoapClient\Command\GetMethodRequestXmlCommand();
    $commands[] = new \PhpSoapClient\Command\ListMethodsCommand();
    return $commands;
  }
}
