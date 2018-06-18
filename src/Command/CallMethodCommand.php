<?php

namespace App\Command;

use App\Command\Base\SoapCommand;
use App\Helper\EditorHelper;
use App\Helper\StdinHelper;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CallMethodCommand extends Command implements LoggerAwareInterface
{
    protected static $defaultName = 'call';

    use LoggerAwareTrait;
    use SoapCommand;

    protected function configure()
    {
        $this->setDescription('Call the remote service with the `method` specified and output the reponse to stdout');

        $this->configureSoapOptions();

        $this->addOption('editor', null, InputOption::VALUE_NONE, 'Open the request xml in your favorite $EDITOR before sending to the server');
        $this->addOption('xml', null, InputOption::VALUE_NONE, 'Output the results as xml. Otherwise output as an object');

        $this->addArgument('method', InputArgument::REQUIRED, 'The name of the remote method');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $method = $input->getArgument('method');

        if (true === $input->getOption('editor')) {
            $editor = new EditorHelper();

            $this->logger->debug('Getting request xml from the server');
            $empty_request = $this->soapClient->__getRequestXmlForMethod($method);

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
                $this->logger->warning('Create xml request below and finish with <info>ctrl+d</info>:');
                $request_xml = $stdin->read(true);
            }
            unset($stdin);
        }

        $this->logger->info("Calling method `$method` on the remote");
        $start_time = microtime(true);

        if (true === $input->getOption('xml')) {
            $response = $this->soapClient->__getResponseXmlForMethod($method, $request_xml);
        } else {
            $response = $this->soapClient->__getResponseObjectForMethod($method, $request_xml);
        }

        $this->logger->debug('Calling method took ' . (microtime(true) - $start_time) . ' seconds');
        unset($start_time);

        $output->writeln(print_r($response, true));
    }
}
