<?php

namespace PhpSoapClient\Helper;

use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\HelperInterface;



class StdinHelper implements HelperInterface
{
  protected $helperset;

  protected $blocking;
  protected $stream;

  public function getName()
  {
    return 'stdin';
  }

  public function setHelperSet(HelperSet $helperSet = null)
  {
    $this->helperset = $helperset;
  }

  public function getHelperSet()
  {
    return $this->helperset;
  }

  public function setBlocking($blocking)
  {
    $this->blocking = (bool) $blocking;
  }

  public function read($blocking=null)
  {
    $stream = fopen('php://stdin', 'r');

    if (false === $stream)
    {
      throw \Exception('Could not fopen php://stdin.');
    }

    if (false === is_null($blocking))
    {
      stream_set_blocking($stream, (bool) $blocking ? 1 : 0);
    }

    $buffer = null;

    while($line = fgets($stream, 4096))
    {
      $buffer .= $line;
    }

    fclose($stream);

    return $buffer;
  }
}
