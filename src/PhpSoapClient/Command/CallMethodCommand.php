<?php

namespace PhpSoapClient\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PhpSoapClient\Command\Base\SoapCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use PhpSoapClient\Helper\EditorHelper;
use PhpSoapClient\Helper\StdinHelper;

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
      'editor',
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
    $service = $this->getSoapClient($input);
    $method = $input->getArgument('method');

    if (true === $input->getOption('editor')) {
      $editor = new EditorHelper();

      $this->logger->debug('Getting request xml from the server');
      $empty_request = $service->__getRequestXmlForMethod($method);

      $this->logger->debug('Starting editor: ' . $editor->getEditor());
      $request_xml = $editor->open_and_read($empty_request);

      unset($editor);

      if (0 === strcmp((string) $request_xml, $empty_request)) {
        $this->logger->debug('File wasn\'t modified');
      } else {
        $this->logger->debug('File was modified using the editor');
      }
    } else {
      $stdin = new StdinHelper();
      $request_xml = $stdin->read(false);

      if (null === $request_xml) {
        $this->logger->info('Create xml request below and finish with <info>ctrl+d</info>:');
        $request_xml = $stdin->read(true);
      }
      unset($stdin);
    }

    $this->logger->debug('Calling method %s on the remote', $method);
    $start_time = microtime(true);

    if (true === $input->getOption('xml')) {
      $response = $service->__getResponseXmlForMethod($method, $request_xml);
    } else {
      $response = $service->__getResponseObjectForMethod($method, $request_xml);
    }

    $this->logger->debug('Calling method took %s seconds', microtime(true) - $start_time);
    unset($start_time);

    $output->writeln(print_r($response, true));
  }
}
