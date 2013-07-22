<?php

namespace PhpSoapClient\Helper;

use PhpSoapClient\File\TmpFile;



class EditorHelper
{
  protected $editor;

  public function __construct($editor=null)
  {
    $this->editor = isset($editor) ? $editor : $_SERVER['EDITOR'];
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
    $tmpfile = new TmpFile();

    if (false === is_null($contents))
    {
      $tmpfile->write($contents);
    }

    $filename = $tmpfile->filename();

    system("$this->editor $filename > `tty`", $retval);

    if (0 !== $retval)
    {
      throw \Exception('Something went wrong with tmp file');
    }

    $data = $tmpfile->read($length);

    $tmpfile->destroy();

    return $data;
  }
}
