<?php

namespace PhpSoapClient\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PhpSoapClient\Command\Base\SoapCommand;
use Symfony\Component\Console\Input\InputArgument;


class GetMethodRequestXmlCommand extends SoapCommand
{
  protected function configure()
  {
    parent::configure();

    $this->setName('request');
    $this->setDescription('Generate an xml formatted SOAP request for the given method and output to stdout.');
    $this->addArgument(
      'method',
      InputArgument::REQUIRED,
      'The name of the remote method.'
    );
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $endpoint = $input->getOption('endpoint');
    $method = $input->getArgument('method');

    if (false === isset($method))
    {
      throw new \Exception('You must specify a method name to call');
    }
    else
    {
      $this->debug($output, sprintf('Generating request for %s on remote', $method));

      $service = $this->getSoapClient($endpoint);

      echo $service->__getRequestXmlForMethod($method);
    }

    $this->debug($output, 'Done.');
  }
}
