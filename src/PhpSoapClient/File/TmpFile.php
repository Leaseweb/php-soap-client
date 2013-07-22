<?php

namespace PhpSoapClient\File;

class TmpFile
{
  protected $file;
  protected $info;

  public function __construct()
  {
    $tmpfile = tmpfile();

    if (false === $tmpfile)
    {
      throw new \Exception('Unable to create a tmp file');
    }

    $this->file = $tmpfile;
    $this->info = stream_get_meta_data($tmpfile);
  }

  public function read($bytes = 2048)
  {
    if (false === $this->is_resource())
    {
      throw \Exception('Lost track of the tmpfile');
    }

    return file_get_contents($this->info['uri']);
  }

  public function write($data, $offset=0)
  {
    if (false === $this->is_resource())
    {
      throw \Exception('Lost track of the tmpfile');
    }

    if (intval($offset) !== ftell($this->file))
    {
      fseek($this->file, $offset);
    }

    $bytes = fwrite($this->file, $data);

    if (false === $bytes)
    {
      throw \Exception('Unable to write contents to tmpfile');
    }

    return $bytes;
  }

  public function is_resource()
  {
    return is_resource($this->file);
  }

  public function destroy()
  {
    if (true === $this->is_resource())
    {
      fclose($this->file);
    }

    if (true === is_file($this->filename()))
    {
      unlink($this->filename());
    }

    unset($this->file, $this->info);
  }

  public function filename()
  {
    return isset($this->info) ? $this->info['uri'] : false;
  }
}
