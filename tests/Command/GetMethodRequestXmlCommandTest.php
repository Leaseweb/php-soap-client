<?php

namespace App\Command;

class GetMethodRequestXmlCommandTest extends BaseCommandTest
{
    public function testExecuteWithoutEndpoint()
    {
        $this->assertEquals(1, $this->app->run(['request']));
        $this->assertRegExp('/You must specify an endpoint./', $this->app->getDisplay());
    }

    public function testExecuteNotEnoughArguments()
    {
        $this->assertEquals(1, $this->app->run([
            'request',
            '--endpoint' => 'http://www.dataaccess.com/webservicesserver/numberconversion.wso?WSDL',
        ]));

        $this->assertRegExp('/Not enough arguments./', $this->app->getDisplay());
    }

    public function testExecute()
    {
        $this->assertEquals(0, $this->app->run([
            'request',
            '--endpoint' => 'http://www.dataaccess.com/webservicesserver/numberconversion.wso?WSDL',
            'method' => 'NumberToWords',
        ]));

        $this->assertRegExp('/ns1:NumberToWords/', $this->app->getDisplay());
        $this->assertRegExp('/SOAP-ENV:Envelope/', $this->app->getDisplay());
        $this->assertRegExp('/SOAP-ENV:Body/', $this->app->getDisplay());
    }
}
