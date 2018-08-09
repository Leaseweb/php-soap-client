<?php

namespace App\Command;

use App\Command\Base\SoapCommand;
use Symfony\Component\Console\Command\Command;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetMethodRequestXmlCommand extends Command implements LoggerAwareInterface
{
    protected static $defaultName = 'request';

    use LoggerAwareTrait;
    use SoapCommand;

    protected function configure()
    {
        $this->setDescription('Generate an xml formatted SOAP request for the given method and output to stdout');

        $this->configureSoapOptions();

        $this->addArgument('method', InputArgument::REQUIRED, 'The name of the remote method');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->soapClient->__getRequestXmlForMethod($input->getArgument('method')));
    }
}
