<?php

namespace PhpSoapClient\Helper;

class EditorHelper
{
  protected $editor;
  protected $temp_file;
  protected $temp_file_info;

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

  protected function create_temp_file()
  {
    if ($this->has_temp_file())
    {
      $this->destroy_temp_file();
    }

    $this->temp_file = tmpfile();
    $this->temp_file_info = stream_get_meta_data($this->temp_file);
  }

  protected function destroy_temp_file()
  {
    if ($this->has_temp_file())
    {
      unlink($this->temp_file);
      unset($this->temp_file, $this->temp_file_info);
    }
  }

  protected function has_temp_file()
  {
    return true === isset($this->temp_file);
  }

  public function open($contents=null)
  {
    $this->create_temp_file();

    if (false === is_null($contents))
    {
      fwrite($this->temp_file, $contents);
    }

    $temp_filename = $this->temp_file_info['uri'];

    system($this->editor . " $temp_filename > `tty`", $retval);

    return $retval;
  }

  public function read($length = 2048)
  {
    if (!$this->has_temp_file())
    {
      throw \Exception('You must create a temporary file first.');
    }

    $temp_filename = $this->temp_file_info['uri'];

    $file = fopen($temp_filename, 'r');
    $contents = fread($file, $length);
    fclose($file);

    return $contents;
  }
}
