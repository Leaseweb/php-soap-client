<?php

namespace App\Test\Command;

class CallMethodCommandTest extends BaseCommandTest
{
    protected $NAME = 'call';

    /**
     * @expectedException         \RuntimeException
     * @expectedExceptionMessage  Not enough arguments.
     */
    public function testExecuteNotEnoughArguments()
    {
        $this->getCommandTester()->execute(array('command' => $this->NAME));
    }

    /**
     * @expectedException         \InvalidArgumentException
     * @expectedExceptionMessage  You must specify an endpoint.
     */
    public function testExecuteWithoutEndpoint()
    {
        $this->getCommandTester()->execute(
      array('command' => $this->NAME, 'stakker')
    );
    }

    /**
     * @expectedException         \SoapFault
     */
    public function testExecuteWithInvalidEndpointAsEnv()
    {
        $_SERVER['SOAPCLIENT_ENDPOINT'] = 'http://www.example.com/webservices/tempconvert.asmx?WSDL';

        $this->getCommandTester()->execute(
      array('command' => $this->NAME, 'stakker')
    );
    }

    /**
     * @expectedException         \SoapFault
     */
    public function testExecuteInvalidEndpoint()
    {
        $this->getCommandTester()->execute(array(
        'command' => $this->NAME,
        '--endpoint' => 'http://www.example.com/webservices/tempconvert.asmx?WSDL',
        'method' => 'stakker',
    ));
    }

    public function testCallingMethodUsingEditorResponseAsXml()
    {
        $GLOBALS['mock_system_retval'] = 0;
        $_SERVER['EDITOR'] = 'nano';

        $tester = $this->getCommandTester();
        $tester->execute(array(
      'command' => $this->NAME,
      '--endpoint' => 'http://www.w3schools.com/webservices/tempconvert.asmx?WSDL',
      '--editor' => true,
      '--xml' => true,
      'method' => 'FahrenheitToCelsius',
    ));
    }

    public function testCallingMethodUsingEditorResponseAsObject()
    {
        $GLOBALS['mock_fgets'] = '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://tempuri.org/">
<SOAP-ENV:Body>
<ns1:FahrenheitToCelsius>
<ns1:Fahrenheit>23</ns1:Fahrenheit>
</ns1:FahrenheitToCelsius>
</SOAP-ENV:Body>
</SOAP-ENV:Envelope>';

        $tester = $this->getCommandTester();
        $tester->execute(array(
      'command' => $this->NAME,
      '--endpoint' => 'http://www.w3schools.com/webservices/tempconvert.asmx?WSDL',
      'method' => 'FahrenheitToCelsius',
    ));
    }
}
