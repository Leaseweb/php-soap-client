<?php

namespace PhpSoapClient\Helper;

class StdinHelper
{
    public function read($blocking=null)
    {
        $stream = fopen('php://stdin', 'r');

        if (false === $stream) {
            throw new \RuntimeException('Could not fopen php://stdin.');
        }

        if (false === is_null($blocking)) {
            stream_set_blocking($stream, (bool) $blocking ? 1 : 0);
        }

        $buffer = null;

        while ($line = fgets($stream, 4096)) {
            $buffer .= $line;
        }

        fclose($stream);

        return $buffer;
    }
}
