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
  }

  protected function getEndpoint(InputInterface $input)
  {
    $endpoint = $input->getOption('endpoint');

    if (false === is_null($endpoint))
    {
      return $endpoint;
    }
    elseif (true === array_key_exists('SOAPCLIENT_ENDPOINT', $_SERVER))
    {
      return $_SERVER['SOAPCLIENT_ENDPOINT'];
    }
    else
    {
      throw new \InvalidArgumentException('You must specify an endpoint.');
    }
  }

  protected function getSoapClient($endpoint, $cache=false, $timeout=120)
  {
    if (true === $cache)
    {
      $this->logger->debug('Enabling caching of wsdl');
      $cache = WSDL_CACHE_MEMORY;
    }
    else
    {
      $this->logger->debug('Wsdls are not being cached.');
      $cache = WSDL_CACHE_NONE;
    }

    ini_set('default_socket_timeout', $timeout);
    $this->logger->debug('Set socket timeout to %s seconds.', $timeout);

    return new SoapClient($endpoint, array(
      'trace' => 1,
      'exceptions' => true,
      'connection_timeout' => $timeout,
      'cache_wsdl' => $cache,
    ));
  }
}
