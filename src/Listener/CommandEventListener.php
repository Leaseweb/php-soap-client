<?php

namespace App\Listener;

use App\Command\Base\SoapCommand;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Event\ConsoleCommandEvent;

class CommandEventListener
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onCommandAction(ConsoleCommandEvent $event): void
    {
        if (!$command = $event->getCommand()) {
            return;
        }

        if (!in_array(SoapCommand::class, class_uses($command))) {
            return;
        }

        $input = $event->getInput();

        // TODO also get data from environment variables now
        // TODO handle endpoint values from configuration

        if (!$input->getOption('endpoint')) {
            throw new InvalidArgumentException('You must specify an endpoint.');
        }

        $this->container->setParameter('soap_endpoint', $input->getOption('endpoint'));
        $this->container->setParameter('soap_cache_wsdl', true === $input->getOption('cache') ? WSDL_CACHE_MEMORY : WSDL_CACHE_NONE);

        ini_set('default_socket_timeout', $this->container->getParameter('timeout'));

        $command->setSoapClient($this->container->get('soap_client'));
    }
}
