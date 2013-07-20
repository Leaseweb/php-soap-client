<?php

namespace PhpSoapClient\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PhpSoapClient\Command\Base\SoapCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use PhpSoapClient\Helper\EditorHelper;


class CallMethodCommand extends SoapCommand
{
  protected function configure()
  {
    parent::configure();

    $this->setName('call');
    $this->setDescription('Call the remote service with the `method` specified and output the reponse to stdout.');

    $this->addArgument(
      'method',
      InputArgument::REQUIRED,
      'The name of the remote method.'
    );

    $this->addOption(
      'use-editor',
      null,
      InputOption::VALUE_NONE,
      'Open the request xml in your favorite $EDITOR before sending to the server.'
    );

    $this->addOption(
      'xml',
      null,
      InputOption::VALUE_NONE,
      'Output the results as xml. Otherwise output as an object.'
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

    $service = $this->getSoapClient($endpoint, $input->getOption('cache'));

    if (true === $input->getOption('use-editor'))
    {
      $helper = new EditorHelper();

      $this->logger->debug('Getting request xml from the server');
      $empty_request = $service->__getRequestXmlForMethod($method);

      $this->logger->debug('Starting editor: ' . $helper->getEditor());
      $result = $helper->open($empty_request);

      if (0 === $result)
      {
        $request_xml = $helper->read();
      }
      else
      {
        throw \Exception('Something went wrong with tmp file');
      }

      if (0 === strcmp((string)$request_xml, $request_xml))
      {
        $this->logger->debug('File wasn\'t modified');
      }
      else
      {
        $this->logger->debug('File was modified using the editor');
      }
    }
    else
    {
      $request_xml = $this->read_from_stdin(true);
    }

    if (null === $request_xml)
    {
      $this->logger->debug('Create xml request below and finish with ctrl+d');
      $request_xml = $this->read_from_stdin(false);
    }

    $this->logger->debug("Calling method $method on the remote");
    $t1 = microtime(true);

    if (true === $input->getOption('xml'))
    {
      $response = $service->__getResponseXmlForMethod($method, $request_xml);
    }
    else
    {
      $response = $service->__getResponseObjectForMethod($method, $request_xml);
    }

    $this->logger->debug('Calling method took %s seconds', microtime(true) - $t1);
    unset($t1);

    print_r($response);

    return 0;
  }
}
