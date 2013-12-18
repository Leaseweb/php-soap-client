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
    $service = $this->getSoapClient($input);
    $method = $input->getArgument('method');

    $this->logger->debug('Generating request for %s on remote', $method);

    $output->writeln($service->__getRequestXmlForMethod($method));

    $this->logger->debug('Done.');
  }
}
