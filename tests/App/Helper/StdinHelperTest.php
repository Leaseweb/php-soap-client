<?php

namespace App\Test\Helper;

use App\Helper\StdinHelper;
use PHPUnit\Framework\TestCase;

class StdinHelperTest extends TestCase
{
    public function testStdinHelper()
    {
        $stdin = new StdinHelper();
        $this->assertEquals(null, $stdin->read(false));

        $GLOBALS['mock_fgets'] = 'stakker';
        $this->assertEquals('stakker', $stdin->read(false));
    }

    /**
     * @expectedException         \RuntimeException
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
