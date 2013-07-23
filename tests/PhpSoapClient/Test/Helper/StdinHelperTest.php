<?php

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
