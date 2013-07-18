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

    $this->setName('listt');
    $this->setDescription('Get a list of available methods to call on the remote.');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $endpoint = $input->getOption('endpoint');
    $method = $input->getArgument('method');

    $service = $this->getSoapClient($endpoint);

    $output->writeln('Listing all available methods on the remote.');

    // echo implode(' ', array_keys($this->remote_service->__getMethods()));

    foreach (array_keys($service->__getMethods()) as $method)
    {
      echo $method.PHP_EOL;
    }
  }
}
