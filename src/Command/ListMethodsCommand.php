<?php

namespace App\Command;

use App\Command\Base\SoapCommand;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListMethodsCommand extends Command implements LoggerAwareInterface
{
    protected static $defaultName = 'list-methods';

    use LoggerAwareTrait;
    use SoapCommand;

    protected function configure()
    {
        $this->setDescription('Get a list of available methods to call on the remote');

        $this->configureSoapOptions();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach (array_keys($this->soapClient->__getMethods()) as $method) {
            $output->writeln((string) $method);
        }

        return 0;
    }
}
