<?php

namespace App\Test;

use App\Application;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    public function testApplicationName()
    {
        $app = new Application();

        $this->assertEquals('php-soap-client', $app->getName());
        $this->assertStringMatchesFormat('%i.%i.%i', $app->getVersion());

        $this->assertInstanceOf('App\Command\GetWsdlCommand', $app->get('wsdl'));
        $this->assertInstanceOf('App\Command\CallMethodCommand', $app->get('call'));
        $this->assertInstanceOf('App\Command\GetMethodRequestXmlCommand', $app->get('request'));
        $this->assertInstanceOf('App\Command\ListMethodsCommand', $app->get('list-methods'));
    }
}
