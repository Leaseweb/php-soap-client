<?php

namespace App\Helper
{
  function fopen($stream, $mode)
  {
      if (array_key_exists('mock_fopen', $GLOBALS)) {
          return false;
      }

      return \fopen($stream, $mode);
  }

  function fgets($stream, $bytes)
  {
      if (array_key_exists('mock_fgets', $GLOBALS)) {
          $data = $GLOBALS['mock_fgets'];
          unset($GLOBALS['mock_fgets']);

          return $data;
      }

      return \fgets($stream, $bytes);
  }

  function system($command, &$retval)
  {
      if (array_key_exists('mock_system_retval', $GLOBALS)) {
          if (!is_null($GLOBALS['mock_system_retval'])) {
              $retval = $GLOBALS['mock_system_retval'];

              return '';
          }
      }

      return \system($command, $retval);
  }
}

namespace App\File
{
  function tmpfile()
  {
      if (array_key_exists('mock_tmpfile', $GLOBALS)) {
          return false;
      }

      return \tmpfile();
  }

  function fwrite($file, $data)
  {
      if (array_key_exists('mock_fwrite', $GLOBALS)) {
          return false;
      }

      return \fwrite($file, $data);
  }
}

// namespace
// {
//   error_reporting(E_ALL);

//   $loader = require __DIR__.'/../src/bootstrap.php';
//   $loader->add('App\Test', __DIR__);
// }
