<?php

namespace App\Command;

class GetWsdlCommandTest extends BaseCommandTest
{
    public function testExecuteWithoutEndpoint()
    {
        $this->assertEquals(1, $this->app->run(['wsdl']));
        $this->assertRegExp('/You must specify an endpoint./', $this->app->getDisplay());
    }

    public function testExecute()
    {
        $this->assertEquals(0, $this->app->run([
            'wsdl',
            '--endpoint' => 'http://www.dataaccess.com/webservicesserver/numberconversion.wso?WSDL',
        ]));

        $this->assertRegExp('/NumberToWords/', $this->app->getDisplay());
    }
}
