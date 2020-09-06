<?php

namespace App\Command;

use App\Command\Base\SoapCommand;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetWsdlCommand extends Command implements LoggerAwareInterface
{
    protected static $defaultName = 'wsdl';

    use LoggerAwareTrait;
    use SoapCommand;

    protected function configure()
    {
        $this->setDescription('Get the WSDL of a soap service');

        $this->configureSoapOptions();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln($this->soapClient->getWsdl());

        return 0;
    }
}
