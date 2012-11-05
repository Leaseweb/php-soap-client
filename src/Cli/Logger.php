<?php

namespace Cli;

class Logger
{
  const DEBUG = 0;
  const INFO = 1;
  const WARN = 2;
  const ERROR = 3;

  protected $level;

  public function __construct($level = 1)
  {
    $this->set_level($level);
  }

  public function set_level($level)
  {
    $this->level = $level;
  }

  public function debug()
  {
    $this->_log_stdout(self::DEBUG, func_get_args());
  }

  public function info()
  {
    $this->_log_stdout(self::INFO, func_get_args());
  }

  public function warn()
  {
    $this->_log_stderr(self::WARN, func_get_args());
  }

  public function error()
  {
    $this->_log_stderr(self::ERROR, func_get_args());
  }

  protected function _log_stdout($level, $args)
  {
    if ($level >= $this->level)
    {
      file_put_contents('php://stdout', call_user_func_array('sprintf', $args) . PHP_EOL);
    }
  }

  protected function _log_stderr($level, $args)
  {
    if ($level >= $this->level)
    {
      file_put_contents('php://stderr', call_user_func_array('sprintf', $args) . PHP_EOL);
    }
  }

  protected function _get_curr_time()
  {
    list($microSec, $timeStamp) = explode(" ", microtime());
    return sprintf('[%s:%s]', date('Y-m-d H:i', $timeStamp), date('s', $timeStamp) + $microSec);
  }
}

