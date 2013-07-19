<?php

namespace Rocco\PhpSoapClient;

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
    $commands[] = new \Rocco\PhpSoapClient\Command\GetWsdlCommand();
    $commands[] = new \Rocco\PhpSoapClient\Command\CallMethodCommand();
    $commands[] = new \Rocco\PhpSoapClient\Command\GetMethodRequestXmlCommand();
    $commands[] = new \Rocco\PhpSoapClient\Command\ListMethodsCommand();
    return $commands;
  }
}
