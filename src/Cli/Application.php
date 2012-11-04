<?php

namespace Cli;

class Application
{
  protected $name;
  protected $help_text;
  protected $params;
  protected $options;

  public function __construct($name)
  {
    $this->name   = $name;
  }

  public function parse_options()
  {
    $short = '';
    $long = array();

    foreach ($this->params as $key => $value)
    {
      $short .= $value['short'];
      $long[] = $value['long'];
    }

    $this->options = getopt($short, $long);
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
    else
    {
      return $default;
    }
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
}
