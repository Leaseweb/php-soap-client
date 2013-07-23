<?php

namespace PhpSoapClient;

use Symfony\Component\Console\Application as BaseApplication;


class Application extends BaseApplication
{
  protected static $NAME = 'php-soap-client';
  protected static $VERSION = '2.1.1';

  public function __construct()
  {
    parent::__construct(self::$NAME, self::$VERSION);
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
