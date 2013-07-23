<?php

namespace PhpSoapClient\Test\Command;

use PhpSoapClient\Application;
use PhpSoapClient\Command\GetWsdlCommand;
use Symfony\Component\Console\Tester\CommandTester;



class GetWsdlCommandTest extends \PHPUnit_Framework_TestCase
{
  protected static $NAME = 'wsdl';

  protected function getCommandTester()
  {
    $application = new Application();
    $command = $application->find(self::$NAME);

    return new CommandTester($command);
  }

  /**
   * @expectedException         InvalidArgumentException
   * @expectedExceptionMessage  You must specify an endpoint.
   */
  public function testExecuteWithoutEndpoint()
  {
    $tester = $this->getCommandTester();
    $tester->execute(array(
      'command' => self::$NAME
    ));
  }

  public function testExecute()
  {
    $tester = $this->getCommandTester();
    $tester->execute(array(
      'command' => self::$NAME,
      '--endpoint' => 'http://www.w3schools.com/webservices/tempconvert.asmx?WSDL',
    ));

    $this->assertRegExp('/FahrenheitToCelsius/', $tester->getDisplay());
    $this->assertRegExp('/CelsiusToFahrenheit/', $tester->getDisplay());
  }
}
