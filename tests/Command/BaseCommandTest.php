<?php

namespace App\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

abstract class BaseCommandTest extends TestCase
{
    protected function setUp()
    {
        unset($GLOBALS['mock_fgets']);
        unset($GLOBALS['mock_system_retval']);
        unset($_SERVER['SOAPCLIENT_ENDPOINT']);
        unset($_SERVER['EDITOR']);

        $container = new ContainerBuilder();

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('app.yml');
        $loader->load('parameters.yml');

        $application = $container->get('symfony.application');
        $application->setAutoExit(false);

        $this->app = new ApplicationTester($application);
    }

    protected function tearDown()
    {
        unset($GLOBALS['mock_fgets']);
        unset($GLOBALS['mock_system_retval']);
        unset($_SERVER['SOAPCLIENT_ENDPOINT']);
        unset($_SERVER['EDITOR']);
    }
}
