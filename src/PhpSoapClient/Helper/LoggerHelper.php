<?php

namespace PhpSoapClient\Helper;

use Symfony\Component\Console\Output\OutputInterface;



class LoggerHelper
{
  protected $_ouput;

  public function __construct(OutputInterface $output)
  {
    $this->_output = $output;
  }

  public function debug()
  {
    $this->_log_stdout(OutputInterface::VERBOSITY_DEBUG, func_get_args());
  }

  protected function _log_stdout($level, $args)
  {
    if ($level <= $this->_output->getVerbosity())
    {
      $this->_output->writeln(call_user_func_array('sprintf', $args));
    }
  }
}
