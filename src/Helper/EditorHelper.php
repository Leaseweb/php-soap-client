<?php

namespace App\Helper;

use App\File\TmpFile;

class EditorHelper
{
    protected $editor;

    public function __construct($editor = null)
    {
        $this->editor = empty($editor) ? @$_SERVER['EDITOR'] : $editor;

        if (true === empty($this->editor)) {
            throw new \InvalidArgumentException('No favorite $EDITOR found');
        }
    }

    public function getEditor()
    {
        return $this->editor;
    }

    public function setEditor($editor)
    {
        $this->editor = $editor;
    }

    public function open_and_read($contents = null)
    {
        $tmpfile = new TmpFile();

        if (false === is_null($contents)) {
            $tmpfile->write($contents);
        }

        $filename = $tmpfile->filename();

        system("$this->editor $filename > `tty`", $retval);

        if (0 !== $retval) {
            throw new \RuntimeException('Something went wrong with tmp file');
        }

        $data = $tmpfile->read();

        $tmpfile->destroy();

        return $data;
    }
}
