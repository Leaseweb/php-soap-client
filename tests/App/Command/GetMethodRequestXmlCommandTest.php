<?php

namespace App\Test\Command;

class GetMethodRequestXmlCommandTest extends BaseCommandTest
{
    protected $NAME = 'request';

    /**
     * @expectedException         \RuntimeException
     * @expectedExceptionMessage  Not enough arguments.
     */
    public function testExecuteNotEnoughArguments()
    {
        $this->getCommandTester()->execute(array(
      'command' => $this->NAME,
    ));
    }

    /**
     * @expectedException         \InvalidArgumentException
     * @expectedExceptionMessage  You must specify an endpoint.
     */
    public function testExecuteWithoutEndpoint()
    {
        $this->getCommandTester()->execute(array(
      'command' => $this->NAME,
      'stakker',
    ));
    }

    public function testExecute()
    {
        $tester = $this->getCommandTester();
        $tester->execute(array(
      'command' => $this->NAME,
      '--endpoint' => 'http://www.w3schools.com/webservices/tempconvert.asmx?WSDL',
      'method' => 'FahrenheitToCelsius',
    ));
        $this->assertRegExp('/ns1:FahrenheitToCelsius/', $tester->getDisplay());
        $this->assertRegExp('/SOAP-ENV:Envelope/', $tester->getDisplay());
        $this->assertRegExp('/SOAP-ENV:Body/', $tester->getDisplay());
        $this->assertRegExp('/ns1:Fahrenheit/', $tester->getDisplay());
    }
}
