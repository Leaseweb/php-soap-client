<?php

namespace PhpSoapClient\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PhpSoapClient\Command\Base\SoapCommand;

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
        $service = $this->getSoapClient($input);

        $this->logger->info('Listing all available methods on the remote.');

        foreach (array_keys($service->__getMethods()) as $method) {
            $output->writeln($method);
        }

        $this->logger->debug('Done.');
    }
}
