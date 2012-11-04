<?php

$stream = fopen('php://stdin', r);

stream_set_blocking($stream, 0);

while($line=fgets($stream))
{
  $text = $text . $line;
}

// while(!feof($stream))
// {
//   $text = $text . fgets($stream, 4096);
// }

var_dump($text);
