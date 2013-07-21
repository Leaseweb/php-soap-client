<?php

namespace PhpSoapClient\Helper;

use PhpSoapClient\File\TmpFile;
use Symfony\Component\Console\Helper\HelperSet;

class EditorHelper implements HelperInterface
{
  protected $helperset;

  protected $editor;
  protected $tmpfile;

  public function getName()
  {
    return 'editor';
  }

  public function setHelperSet(HelperSet $helperSet = null)
  {
    $this->helperset = $helperset;
  }

  public function getHelperSet()
  {
    return $this->helperset;
  }

  public function __construct($editor=null)
  {
    if (false === isset($editor))
    {
      $editor = $_SERVER['EDITOR'];
    }

    $this->editor = $editor;
  }

  public function getEditor()
  {
    return $this->editor;
  }

  public function setEditor($editor)
  {
    $this->editor = $editor;
  }

  public function open_and_read($contents=null, $length = 2048)
  {
    if (false === isset($this->tmpfile))
    {
      $this->tmpfile = new TmpFile();
    }

    if (false === is_null($contents))
    {
      $this->tmpfile->write($contents);
    }

    $command = sprintf('%s %s > `tty`', $this->editor, $this->tmpfile->filename());
    system($command, $retval);

    if (0 !== $retval)
    {
      throw \Exception('Something went wrong with tmp file');
    }

    $data = $this->tmpfile->read($length);

    $this->tmpfile->destroy();
    unset($this->tmpfile);

    return $data;
  }
}
