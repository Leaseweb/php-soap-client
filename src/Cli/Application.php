<?php

namespace Cli;

class Application
{
  protected $name;
  protected $help_text;

  protected $params = array();
  protected $options = array();
  protected $arguments = array();

  private $_options_parsed = false;
  private $_arguments_parsed = false;

  public function __construct($name)
  {
    $this->name = $name;
  }

  public function parse_options()
  {
    if (true === $this->_options_parsed)
    {
      return;
    }

    $short = '';
    $long = array();

    foreach ($this->params as $key => $value)
    {
      $short .= $value['short'];
      $long[] = $value['long'];
    }

    $this->options = getopt($short, $long);

    $this->_options_parsed = true;
  }

  public function parse_arguments()
  {
    if (true === $this->_arguments_parsed)
    {
      return;
    }

    $this->arguments = $_SERVER['argv'];

    $pruneargv = array();
    foreach ($this->options as $option => $value)
    {
      foreach ($this->arguments as $key => $chunk)
      {
        $regex = '/^'. (isset($option[1]) ? '--' : '-') . $option . '/';
        if ($chunk == $value && $this->arguments[$key-1][0] == '-' || preg_match($regex, $chunk))
        {
          array_push($pruneargv, $key);
        }
      }
    }

    while ($key = array_pop($pruneargv)) 
    {
      unset($this->arguments[$key]);
    }

    $this->arguments = array_values($this->arguments);
    $this->_arguments_parsed = true;
  }

  public function has_option($option)
  {
    if (true === array_key_exists(rtrim($this->params[$option]['long'], ':'), $this->options))
    {
      return true;
    }
    elseif (true === array_key_exists(rtrim($this->params[$option]['short'], ':'), $this->options))
    {
      return true;
    }
    elseif (true === array_key_exists('SOAP_'.strtoupper($option), $_SERVER))
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  public function get_option($option, $default = null)
  {
    if (true === array_key_exists(rtrim($this->params[$option]['long'], ':'), $this->options))
    {
      return $this->options[rtrim($this->params[$option]['long'], ':')];
    }
    elseif (true === array_key_exists(rtrim($this->params[$option]['short'], ':'), $this->options))
    {
      return $this->options[rtrim($this->params[$option]['short'], ':')];
    }
    elseif (true === array_key_exists('SOAP_'.strtoupper($option), $_SERVER))
    {
      return $_SERVER['SOAP_'.strtoupper($option)];
    }
    elseif (true === array_key_exists('default', $this->params[$option]))
    {
      return $this->params[$option]['default'];
    }
    else
    {
      return $default;
    }
  }

  public function get_argument($index, $default = null)
  {
    if (true === array_key_exists($index, $this->arguments))
    {
      return $this->arguments[$index];
    }
    else
    {
      return $default;
    }
  }

  public function get_arguments()
  {
    return $this->arguments;
  }

  public function set_params($params)
  {
    $this->params = $params;
  }

  public function get_help()
  {
    return sprintf($this->help_text, $this->name);
  }

  public function set_help($help_text)
  {
    $this->help_text = $help_text;
  }

  public function read_from_stdin($non_blocking = false)
  {
    $stream = fopen('php://stdin', 'r');

    $buffer = null;

    if (true === $non_blocking)
    {
      stream_set_blocking($stream, 0);
    }

    while($line = fgets($stream, 4096))
    {
      $buffer .= $line;
    }

    fclose($stream);

    return $buffer;
  }
}
