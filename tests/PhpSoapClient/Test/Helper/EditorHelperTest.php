<?php

namespace PhpSoapClient\Helper;

function system($command, &$retval)
{
  if (array_key_exists('mock_system_retval', $GLOBALS))
  {
    if (!is_null($GLOBALS['mock_system_retval']))
    {
      $retval = $GLOBALS['mock_system_retval'];
      return '';
    }
  }

  return \system($command, $retval);
}


namespace PhpSoapClient\Test\Helper;

use PhpSoapClient\Helper\EditorHelper;



class EditorHelperTest extends \PHPUnit_Framework_TestCase
{
  public function testDefaultEditor()
  {
    $current_editor = $_SERVER['EDITOR'];
    $_SERVER['EDITOR'] = 'vim';

    $editor = new EditorHelper();

    $this->assertEquals(
      'vim',
      $editor->getEditor(),
      '::getEditor() gets the name of the editor'
    );

    $_SERVER['EDITOR'] = $current_editor;
  }
  
  /**
   * @expectedException   InvalidArgumentException
   * @expectedExceptionMessage  No favorite $EDITOR found
   */
  public function testNoValidEditor()
  {
    $current_editor = $_SERVER['EDITOR'];

    $_SERVER['EDITOR'] = '';
    $editor = new EditorHelper();

    unset($_SERVER['EDITOR']);
    $editor = new EditorHelper();

    $_SERVER['EDITOR'] = $current_editor;
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

  protected function tearDown()
  {
    unset($GLOBALS['mock_system_retval']);
  }
}
