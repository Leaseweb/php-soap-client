<?php

namespace App\Command;

class CallMethodCommandTest extends BaseCommandTest
{
    public function testExecuteWithoutEndpoint()
    {
        $this->assertEquals(1, $this->app->run(['call']));
        $this->assertRegExp('/You must specify an endpoint./', $this->app->getDisplay());
    }

    public function testExecuteNotEnoughArguments()
    {
        $this->assertEquals(1, $this->app->run([
            'call',
            '--endpoint' => 'http://www.dataaccess.com/webservicesserver/numberconversion.wso?WSDL',
        ]));

        $this->assertRegExp('/Not enough arguments/', $this->app->getDisplay());
    }

    public function testExecuteInvalidEndpoint()
    {
        $this->assertEquals(1, $this->app->run([
            'call',
            '--endpoint' => 'http://127.0.0.1/webservicesserver/numberconversion.wso?WSDL',
            'method' => 'stakker',
        ]));

        $this->assertRegExp("/SOAP-ERROR: Parsing WSDL: Couldn't load from/", $this->app->getDisplay());
    }

    // public function testCallingMethodUsingEditorResponseAsXml()
    // {
    //     $GLOBALS['mock_system_retval'] = 0;
    //     $_SERVER['EDITOR'] = 'nano';

    //     $tester = $this->getCommandTester();
    //     $tester->execute([
    //         'command' => 'call',
    //         '--endpoint' => 'http://www.w3schools.com/webservices/tempconvert.asmx?WSDL',
    //         '--editor' => true,
    //         '--xml' => true,
    //         'method' => 'FahrenheitToCelsius',
    //     ]);
    // }

    // public function testCallingMethodUsingEditorResponseAsObject()
    // {
    //     $GLOBALS['mock_fgets'] = '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://tempuri.org/">
    //         <SOAP-ENV:Body>
    //         <ns1:FahrenheitToCelsius>
    //         <ns1:Fahrenheit>23</ns1:Fahrenheit>
    //         </ns1:FahrenheitToCelsius>
    //         </SOAP-ENV:Body>
    //         </SOAP-ENV:Envelope>';

    //     $tester = $this->getCommandTester();
    //     $tester->execute([
    //         'command' => 'call',
    //         '--endpoint' => 'http://www.w3schools.com/webservices/tempconvert.asmx?WSDL',
    //         'method' => 'FahrenheitToCelsius',
    //     ]);
    // }
}
