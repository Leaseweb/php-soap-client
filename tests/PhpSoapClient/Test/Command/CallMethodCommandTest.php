<?php

namespace PhpSoapClient\Test\Command;

use PhpSoapClient\Application;
use PhpSoapClient\Command\GetWsdlCommand;
use Symfony\Component\Console\Tester\CommandTester;



class CallMethodCommandTest extends \PHPUnit_Framework_TestCase
{
  protected static $NAME = 'call';

  protected function getCommandTester()
  {
    $application = new Application();
    $command = $application->find(self::$NAME);

    return new CommandTester($command);
  }

  /**
   * @expectedException         RuntimeException
   * @expectedExceptionMessage  Not enough arguments.
   */
  public function testExecuteNotEnoughArguments()
  {
    $this->getCommandTester()->execute(array('command' => self::$NAME));
  }

  /**
   * @expectedException         InvalidArgumentException
   * @expectedExceptionMessage  You must specify an endpoint.
   */
  public function testExecuteWithoutEndpoint()
  {
    $this->getCommandTester()->execute(
      array('command' => self::$NAME, 'stakker')
    );
  }

  /**
   * @expectedException         SoapFault
   */
  public function testExecuteWithInvalidEndpointAsEnv()
  {
    $_SERVER['SOAPCLIENT_ENDPOINT'] = 'http://www.example.com/webservices/tempconvert.asmx?WSDL';

    $this->getCommandTester()->execute(
      array('command' => self::$NAME, 'stakker')
    );
  }

  /**
   * @expectedException         SoapFault
   */
  public function testExecuteInvalidEndpoint()
  {
    $this->getCommandTester()->execute(array(
        'command' => self::$NAME,
        '--endpoint' => 'http://www.example.com/webservices/tempconvert.asmx?WSDL',
        'method' => 'stakker'
    ));
  }

  protected function tearDown()
  {
    unset($_SERVER['SOAPCLIENT_ENDPOINT']);
  }
}
