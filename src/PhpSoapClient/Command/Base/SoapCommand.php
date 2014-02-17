<?php

namespace PhpSoapClient\Command\Base;

use PhpSoapClient\Client\SoapClient;
use PhpSoapClient\Helper\LoggerHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SoapCommand extends Command
{
    protected $logger;

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->logger = new LoggerHelper($output);

        parent::initialize($input, $output);
    }

    protected function getEndpoint(InputInterface $input)
    {
        $endpoint = $input->getOption('endpoint');

        if (false === is_null($endpoint)) {
            return $endpoint;
        } elseif (true === array_key_exists('SOAPCLIENT_ENDPOINT', $_SERVER)) {
            return $_SERVER['SOAPCLIENT_ENDPOINT'];
        } else {
            throw new \InvalidArgumentException('You must specify an endpoint.');
        }
    }

    protected function getSoapClient(InputInterface $input, $timeout=120)
    {
        $cache = $input->getOption('cache');
        $endpoint = $this->getEndpoint($input);

        $this->logger->info('Discovering endpoint ' . $endpoint);

        if (true === $cache) {
            $this->logger->debug('Enabling caching of wsdl');
            $cache = WSDL_CACHE_MEMORY;
        } else {
            $this->logger->debug('Wsdls are not being cached.');
            $cache = WSDL_CACHE_NONE;
        }

        ini_set('default_socket_timeout', $timeout);
        $this->logger->debug("Set socket timeout to $timeout seconds.");

        return new SoapClient($endpoint, array(
            'logger' => $this->logger,
            'trace' => 1,
            'exceptions' => true,
            'connection_timeout' => $timeout,
            'cache_wsdl' => $cache,
        ));
    }
}
