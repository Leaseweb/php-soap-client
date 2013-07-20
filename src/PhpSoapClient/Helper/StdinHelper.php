<?php

namespace PhpSoapClient\Helper;


class StdinHelper
{
  protected $blocking;
  protected $stream;

  public function __construct()
  {
    $this->stream = fopen('php://stdin', 'r');
  }

  public function getBlocking()
  {
    return $this->blocking;
  }

  public function setBlocking($blocking)
  {
    $this->blocking = (bool) $blocking;
    stream_set_blocking($this->stream, $this->blocking ? 1 : 0);
  }

  public function read()
  {
    if (false === $this->is_resource())
    {
      throw \Exception('Connection to stdin lost');
    }

    $buffer = null;

    while($line = fgets($this->stream, 4096))
    {
      $buffer .= $line;
    }

    return $buffer;
  }

  public function is_resource()
  {
    return is_resource($this->stream);
  }

  public function destroy()
  {
    if (true === $this->is_resource())
    {
      fclose($this->stream);
    }
  }
}
