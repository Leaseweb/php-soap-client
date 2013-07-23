<?php

namespace PhpSoapClient\Test\Command;

use PhpSoapClient\Command\GetWsdlCommand;



class ListMethodsCommandTest extends BaseCommandTest
{
  protected $NAME = 'list-methods';

  /**
   * @expectedException         InvalidArgumentException
   * @expectedExceptionMessage  You must specify an endpoint.
   */
  public function testExecuteWithoutEndpoint()
  {
    $this->getCommandTester()->execute(array(
      'command' => $this->NAME
    ));
  }

  public function testExecuteWithCacheAndVerbose()
  {
    $tester = $this->getCommandTester();
    $tester->execute(array(
      'command' => $this->NAME,
      '--verbose' => 3,
      '--cache' => true,
      '--endpoint' => 'http://www.w3schools.com/webservices/tempconvert.asmx?WSDL',
    ));

    $this->assertRegExp('/FahrenheitToCelsius/', $tester->getDisplay());
    $this->assertRegExp('/CelsiusToFahrenheit/', $tester->getDisplay());
  }

  /**
   * @expectedException         SoapFault
   */
  public function testExecuteInvalidEndpoint()
  {
    $this->getCommandTester()->execute(array(
        'command' => $this->NAME,
        '--endpoint' => 'http://www.example.com/webservices/tempconvert.asmx?WSDL',
    ));
  }
}
