<?php

namespace PhpSoapClient\Test\Command;

use PhpSoapClient\Application;
use Symfony\Component\Console\Tester\CommandTester;



abstract class BaseCommandTest extends \PHPUnit_Framework_TestCase
{
  protected $NAME;

  protected function getCommandTester()
  {
    $application = new Application();
    $command = $application->find($this->NAME);

    return new CommandTester($command);
  }

  protected function setUp()
  {
    unset($GLOBALS['mock_fgets']);
    unset($GLOBALS['mock_system_retval']);
    unset($_SERVER['SOAPCLIENT_ENDPOINT']);
    unset($_SERVER['EDITOR']);
  }

  protected function tearDown()
  {
    unset($GLOBALS['mock_fgets']);
    unset($GLOBALS['mock_system_retval']);
    unset($_SERVER['SOAPCLIENT_ENDPOINT']);
    unset($_SERVER['EDITOR']);
  }
}
