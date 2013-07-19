<?php

namespace Rocco\PhpSoapClient\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Rocco\PhpSoapClient\Command\Base\SoapCommand;


class ListMethodsCommand extends SoapCommand
{
  protected function configure()
  {
    parent::configure();

    $this->setName('list-methods');
    $this->setDescription('Get a list of available methods to call on the remote.');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $endpoint = $input->getOption('endpoint');
    $service = $this->getSoapClient($endpoint);

    $this->debug($output, 'Listing all available methods on the remote.');

    // echo implode(' ', array_keys($this->remote_service->__getMethods()));

    foreach (array_keys($service->__getMethods()) as $method)
    {
      echo $method.PHP_EOL;
    }

    $this->debug($output, 'Done.');
  }
}
