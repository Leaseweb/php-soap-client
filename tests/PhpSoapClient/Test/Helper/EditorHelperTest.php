<?php

namespace PhpSoapClient\Test\Helper;

use PhpSoapClient\Helper\EditorHelper;



class EditorHelperTest extends \PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    unset($GLOBALS['mock_system_retval']);
    $_SERVER['EDITOR'] = '';
  }

  public function tearDown()
  {
    unset($GLOBALS['mock_system_retval']);
    $_SERVER['EDITOR'] = '';
  }

  public function testDefaultEditor()
  {
    $_SERVER['EDITOR'] = 'vim';

    $editor = new EditorHelper();

    $this->assertEquals(
      'vim',
      $editor->getEditor(),
      '::getEditor() gets the name of the editor'
    );
  }
  
  /**
   * @expectedException   InvalidArgumentException
   * @expectedExceptionMessage  No favorite $EDITOR found
   */
  public function testNoValidEditor()
  {
    unset($_SERVER['EDITOR']);
    $editor = new EditorHelper();
  }

  /**
   * @expectedException   InvalidArgumentException
   * @expectedExceptionMessage  No favorite $EDITOR found
   */
  public function testEditorEmptyString()
  {
    $_SERVER['EDITOR'] = '';
    $editor = new EditorHelper();
  }

  public function testSetSpecificEditor()
  {
    $editor = new EditorHelper('nano');

    $this->assertEquals(
      'nano',
      $editor->getEditor(),
      '::getEditor() gets the name of the editor'
    );

    $editor->setEditor('vim');

    $this->assertEquals(
      'vim',
      $editor->getEditor(),
      '::getEditor() gets the name of the editor'
    );
  }

  /**
   * @expectedException         RuntimeException
   * @expectedExceptionMessage  Something went wrong with tmp file
   */
  public function testEditorAborts()
  {
    $GLOBALS['mock_system_retval'] = 1;

    $editor = new EditorHelper('vim');
    $content = $editor->open_and_read('test');
  }

  public function testEditor()
  {
    $GLOBALS['mock_system_retval'] = 0;

    $editor = new EditorHelper('vim');
    $content = $editor->open_and_read('test');
  }
}
