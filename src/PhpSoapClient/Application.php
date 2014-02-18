<?php

namespace PhpSoapClient;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputOption;

class Application extends BaseApplication
{
    protected static $NAME = 'php-soap-client';
    protected static $VERSION = '2.1.4';

    public function __construct()
    {
        parent::__construct(self::$NAME, self::$VERSION);
    }

    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();

        $commands[] = new \PhpSoapClient\Command\GetWsdlCommand();
        $commands[] = new \PhpSoapClient\Command\CallMethodCommand();
        $commands[] = new \PhpSoapClient\Command\GetMethodRequestXmlCommand();
        $commands[] = new \PhpSoapClient\Command\ListMethodsCommand();

        return $commands;
    }

    protected function getDefaultInputDefinition()
    {
        $input_definition = parent::getDefaultInputDefinition();

        $input_definition->addOption(
            new InputOption('endpoint', null, InputOption::VALUE_REQUIRED, 'Specify the url to the wsdl of the SOAP webservice to inspect.')
        );

        $input_definition->addOption(
            new InputOption('proxy', null, InputOption::VALUE_REQUIRED, 'Use this proxy to connect to the SOAP web service. E.g: --proxy=my.proxy.com:8080')
        );

        $input_definition->addOption(
            new InputOption('cache', null, InputOption::VALUE_NONE, 'Flag to enable caching of the wsdl. By default this is disabled.')
        );

        return $input_definition;
    }
}
