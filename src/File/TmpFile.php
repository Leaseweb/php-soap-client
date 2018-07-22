<?php

namespace App\File;

class TmpFile
{
    protected $file;
    protected $info;
    protected $mtime;

    public function __construct()
    {
        $tmpfile = tmpfile();

        if (!is_resource($tmpfile)) {
            throw new \RuntimeException('Unable to create a tmp file');
        }

        $this->file = $tmpfile;
        $this->info = stream_get_meta_data($tmpfile);
        $this->mtime = filemtime($this->filename());
    }

    public function read()
    {
        if (false === $this->is_resource()) {
            throw new \RuntimeException('Lost track of the tmpfile');
        }

        return file_get_contents($this->info['uri']);
    }

    public function write($data, $offset = 0)
    {
        if (false === $this->is_resource()) {
            throw new \RuntimeException('Lost track of the tmpfile');
        }

        if (intval($offset) !== ftell($this->file)) {
            fseek($this->file, $offset);
        }

        $bytes = fwrite($this->file, $data);

        if (false === $bytes) {
            throw new \RuntimeException('Unable to write contents to tmpfile');
        }

        $this->mtime = filemtime($this->filename());

        return $bytes;
    }

    public function is_resource()
    {
        return is_resource($this->file);
    }

    public function wasExternallyModified()
    {
        if (false === $this->is_resource()) {
            throw new \RuntimeException('Lost track of the tmpfile');
        }

        return $this->mtime !== filemtime($this->filename());
    }

    public function destroy()
    {
        if (true === $this->is_resource()) {
            fclose($this->file);
        }

        if (true === is_file($this->filename())) {
            @unlink($this->filename());
        }

        $this->file = null;
        $this->info = null;
    }

    public function filename()
    {
        return isset($this->info) ? $this->info['uri'] : false;
    }

    public function mtime()
    {
        return $this->mtime;
    }
}
