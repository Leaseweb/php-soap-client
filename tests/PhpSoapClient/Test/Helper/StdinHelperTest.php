<?php

namespace PhpSoapClient\Helper;

function fopen($stream, $mode)
{
  if (array_key_exists('mock_fopen', $GLOBALS))
  {
    return false;
  }
  return \fopen($stream, $mode);
}

function fgets($stream, $bytes)
{
  if (array_key_exists('mock_fgets', $GLOBALS))
  {
    $data = $GLOBALS['mock_fgets'];
    unset($GLOBALS['mock_fgets']);
    return $data;
  }
  return \fgets($stream, $bytes);
}


namespace PhpSoapClient\Test\Helper;

use PhpSoapClient\Helper\StdinHelper;



class StdinHelperTest extends \PHPUnit_Framework_TestCase
{
  public function testStdinHelper()
  {
    $stdin = new StdinHelper();
    $this->assertEquals(null, $stdin->read(false));

    $GLOBALS['mock_fgets'] = 'stakker';
    $this->assertEquals('stakker', $stdin->read(false));
  }

  /**
   * @expectedException         RuntimeException
   * @expectedExceptionMessage  Could not fopen php://stdin.
   */
  public function testStdinErrorOpenSteam()
  {
    $GLOBALS['mock_fopen'] = true;

    $stdin = new StdinHelper();
    $stdin->read(false);
  }

  protected function tearDown()
  {
    unset($GLOBALS['mock_fopen']);
  }
}
