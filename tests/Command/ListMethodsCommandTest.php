<?php

namespace App\Command;

class ListMethodsCommandTest extends BaseCommandTest
{
    public function testExecuteWithoutEndpoint()
    {
        $this->assertEquals(1, $this->app->run(['list-methods']));
        $this->assertRegExp('/You must specify an endpoint./', $this->app->getDisplay());
    }

    public function testExecuteWithCacheAndVerbose()
    {
        $this->assertEquals(0, $this->app->run([
            'command' => 'list-methods',
            '--verbose' => 3,
            '--cache' => true,
            '--endpoint' => 'http://www.dataaccess.com/webservicesserver/numberconversion.wso?WSDL',
        ]));

        $this->assertRegExp('/NumberToWords/', $this->app->getDisplay());
    }

    public function testExecuteInvalidEndpoint()
    {
        $this->assertEquals(1, $this->app->run([
            'command' => 'list-methods',
            '--endpoint' => 'http://127.0.0.1/webservicesserver/numberconversion.wso?WSDL',
        ]));

        $this->assertRegExp("/SOAP-ERROR: Parsing WSDL: Couldn't load from/", $this->app->getDisplay());
    }
}
