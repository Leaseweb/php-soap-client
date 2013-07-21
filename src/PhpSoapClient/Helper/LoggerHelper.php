<?php

namespace PhpSoapClient\Helper;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\HelperInterface;



class LoggerHelper implements HelperInterface
{
  protected $helperset;

  protected $_ouput;

  public function getName()
  {
    return 'logger';
  }

  public function setHelperSet(HelperSet $helperSet = null)
  {
    $this->helperset = $helperset;
  }

  public function getHelperSet()
  {
    return $this->helperset;
  }

  public function __construct(OutputInterface $output)
  {
    $this->_output = $output;
  }

  public function debug()
  {
    $this->_log_stdout(OutputInterface::VERBOSITY_DEBUG, func_get_args());
  }

  public function info()
  {
    $this->_log_stdout(OutputInterface::VERBOSITY_NORMAL, func_get_args());
  }

  protected function _log_stdout($level, $args)
  {
    if ($level <= $this->_output->getVerbosity())
    {
      $this->_output->writeln(call_user_func_array('sprintf', $args));
    }
  }

  // protected function _get_curr_time()
  // {
  //   list($microSec, $timeStamp) = explode(" ", microtime());
  //   return sprintf('[%s:%s]', date('Y-m-d H:i', $timeStamp), date('s', $timeStamp) + $microSec);
  // }
}
