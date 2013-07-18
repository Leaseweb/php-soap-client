<?php

namespace Rocco\PhpSoapClient\Command\Base;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Rocco\PhpSoapClient\Client\SoapClient;


class SoapCommand extends Command
{
  protected function configure()
  {
    parent::configure();

    $this->addOption(
      'endpoint',
      null,
      InputOption::VALUE_REQUIRED,
      'Specify the url to the wsdl of the SOAP webservice to inspect.'
    );

    $this->addOption(
      'cache',
      null,
      InputOption::VALUE_NONE,
      'Flag to enable caching of the wsdl. By default this is disabled.'
    );
  }

  protected function debug($output, $message)
  {
    if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity())
    {
      $output->writeln($message);
    }
  }

  protected function getSoapClient($endpoint, $cache=false, $timeout=120)
  {
    if (empty($endpoint))
    {
      throw new \Exception('You must specify an endpoint.');
    }

    if (true === $cache)
    {
      // $this->log->debug('Enabling caching of wsdl');
      $cache = WSDL_CACHE_MEMORY;
    }
    else
    {
      // $this->log->debug('Wsdls are not being cached.');
      $cache = WSDL_CACHE_NONE;
    }

    ini_set('default_socket_timeout', $timeout);

    return new SoapClient($endpoint, array(
      'trace' => 1,
      'exceptions' => true,
      'connection_timeout' => $timeout,
      'cache_wsdl' => $cache,
    ));
  }
}
