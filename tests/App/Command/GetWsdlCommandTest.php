<?php

namespace App\Test\Command;

class GetWsdlCommandTest extends BaseCommandTest
{
    protected $NAME = 'wsdl';

    /**
     * @expectedException         \InvalidArgumentException
     * @expectedExceptionMessage  You must specify an endpoint.
     */
    public function testExecuteWithoutEndpoint()
    {
        $tester = $this->getCommandTester();
        $tester->execute(array(
      'command' => $this->NAME,
    ));
    }

    public function testExecute()
    {
        $tester = $this->getCommandTester();
        $tester->execute(array(
      'command' => $this->NAME,
      '--endpoint' => 'http://www.w3schools.com/webservices/tempconvert.asmx?WSDL',
    ));

        $this->assertRegExp('/FahrenheitToCelsius/', $tester->getDisplay());
        $this->assertRegExp('/CelsiusToFahrenheit/', $tester->getDisplay());
    }
}
