<?php

namespace PhpSoapClient\Helper;

use Symfony\Component\Console\Output\OutputInterface;

use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;
use Psr\Log\AbstractLogger;

class LoggerHelper extends AbstractLogger
{
    /**
     * mapping between Psr\Log\LogLevel and the Symfony Console OutputInterface
     *
     * @var array
     */
    protected $mapping = array(
        LogLevel::ALERT => OutputInterface::VERBOSITY_QUIET,
        LogLevel::CRITICAL => OutputInterface::VERBOSITY_QUIET,
        LogLevel::ERROR => OutputInterface::VERBOSITY_QUIET,
        LogLevel::WARNING => OutputInterface::VERBOSITY_NORMAL,
        LogLevel::NOTICE => OutputInterface::VERBOSITY_VERBOSE,
        LogLevel::INFO => OutputInterface::VERBOSITY_VERY_VERBOSE,
        LogLevel::DEBUG => OutputInterface::VERBOSITY_DEBUG,
    );

    protected $_ouput;

    public function __construct(OutputInterface $output)
    {
        $this->_output = $output;
    }

    public function log($level, $message, array $context = array())
    {
        if ($this->mapping[$level] <= $this->_output->getVerbosity()) {
            $this->_output->writeln(sprintf("[$level] $message"));
        }
    }
}
