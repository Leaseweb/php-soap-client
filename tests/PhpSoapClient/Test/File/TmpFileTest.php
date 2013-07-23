<?php

namespace PhpSoapClient\Test\File;

use PhpSoapClient\File\TmpFile;



class TmpFileTest extends \PHPUnit_Framework_TestCase
{
  public function testTmpFile()
  {
    $tmpfile = new TmpFile();
    $time = time();

    $this->assertEquals($time, $tmpfile->mtime());
    $this->assertEquals(false, $tmpfile->wasExternallyModified());

    $tmpfile->write('stakker');

    $this->assertEquals('stakker', $tmpfile->read());
    $this->assertEquals(false, $tmpfile->wasExternallyModified());

    $tmpfile->write('bih');

    $this->assertEquals('bihkker', $tmpfile->read());
    $this->assertEquals(false, $tmpfile->wasExternallyModified());

    $tmpfile->destroy();
  }

  /**
   * @expectedException         RuntimeException
   * @expectedExceptionMessage  Lost track of the tmpfile
   */
  public function testReadNonExistentTmpFile()
  {
    $tmpfile = new TmpFile();
    $tmpfile->destroy();
    $tmpfile->read();
  }

  /**
   * @expectedException         RuntimeException
   * @expectedExceptionMessage  Lost track of the tmpfile
   */
  public function testWriteNonExistentTmpFile()
  {
    $tmpfile = new TmpFile();
    $tmpfile->destroy();
    $tmpfile->write('test');
  }

  /**
   * @expectedException         RuntimeException
   * @expectedExceptionMessage  Lost track of the tmpfile
   */
  public function testExternallyModifiedNonExistentTmpFile()
  {
    $tmpfile = new TmpFile();
    $tmpfile->destroy();
    $tmpfile->wasExternallyModified();
  }

  /**
   * @expectedException         RuntimeException
   * @expectedExceptionMessage  Unable to write contents to tmpfile
   */
  public function testUnableToWriteToTmpFile()
  {
    $GLOBALS['mock_fwrite'] = true;

    $tmpfile = new TmpFile();
    $tmpfile->write('stakker');
  }

  /**
   * @expectedException         RuntimeException
   * @expectedExceptionMessage  Unable to create a tmp file
   */
  public function testUnableToCreateTmpFile()
  {
    $GLOBALS['mock_tmpfile'] = true;

    $tmpfile = new TmpFile();
  }

  protected function tearDown()
  {
    unset($GLOBALS['mock_fwrite']);
    unset($GLOBALS['mock_tmpfile']);
  }
}
