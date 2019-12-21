<?php

namespace App;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ApplicationTest extends TestCase
{
    public function testApplicationName()
    {
        $container = new ContainerBuilder();
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../config'));
        $loader->load('app.yml');
        $loader->load('parameters.yml');

        $app = $container->get('symfony.application');

        $this->assertEquals('php-soap-client', $app->getName());
        $this->assertStringMatchesFormat('%i.%i.%i', $app->getVersion());

        $this->assertInstanceOf('App\Command\GetWsdlCommand', $app->get('wsdl'));
        $this->assertInstanceOf('App\Command\CallMethodCommand', $app->get('call'));
        $this->assertInstanceOf('App\Command\GetMethodRequestXmlCommand', $app->get('request'));
        $this->assertInstanceOf('App\Command\ListMethodsCommand', $app->get('list-methods'));
    }
}
