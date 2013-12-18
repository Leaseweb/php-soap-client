<?php

namespace PhpSoapClient\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PhpSoapClient\Command\Base\SoapCommand;

class GetWsdlCommand extends SoapCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('wsdl');
        $this->setDescription('Get the WSDL of a soap service.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $endpoint = $this->getEndpoint($input);

        $this->logger->debug('Exploring wsdl at %s', $endpoint);
        $output->writeln(file_get_contents($endpoint));
        $this->logger->debug('Done');
    }
}
