<?php

namespace App\Command\Base;

use App\Client\SoapClient;
use Symfony\Component\Console\Input\InputOption;

trait SoapCommand
{
    protected $soapClient;

    public function setSoapClient(SoapClient $soapClient): void
    {
        $this->soapClient = $soapClient;
    }

    protected function configureSoapOptions(): void
    {
        $this->addOption('config', null, InputOption::VALUE_REQUIRED, 'The location to the configuration file', 'soap_client.yml');
        $this->addOption('endpoint', null, InputOption::VALUE_REQUIRED, 'Specify the url to the wsdl of the SOAP webservice to inspect');
        $this->addOption('proxy', null, InputOption::VALUE_REQUIRED, 'Use this proxy to connect to the SOAP web service, e.g: --proxy=my.proxy.com:8080');
        $this->addOption('cache', null, InputOption::VALUE_NONE, 'Flag to enable caching of the wsdl. By default this is disabled');
    }
}
