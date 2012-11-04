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
    $this->log(self::DEBUG, func_get_args());
  }

  public function info()
  {
    $this->log(self::INFO, func_get_args());
  }

  public function error()
  {
    $this->log(self::ERROR, func_get_args());
  }

  protected function log($level, $args)
  {
    if ($level >= $this->level)
    {
      echo call_user_func_array('sprintf', $args) . PHP_EOL;
    }
  }
}

