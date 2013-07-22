<?php

namespace PhpSoapClient\Test;


use PhpSoapClient\Application;



class ApplicationTest extends \PHPUnit_Framework_TestCase
{
  public function testApplicationName()
  {
    $app = new Application();

    $this->assertEquals('php-soap-client', $app->getName());
    $this->assertStringMatchesFormat('%i.%i.%i', $app->getVersion());

    $this->assertInstanceOf(
      'PhpSoapClient\Command\GetWsdlCommand', $app->get('wsdl'));
    $this->assertInstanceOf(
      'PhpSoapClient\Command\CallMethodCommand', $app->get('call'));
    $this->assertInstanceOf(
      'PhpSoapClient\Command\GetMethodRequestXmlCommand', $app->get('request'));
    $this->assertInstanceOf(
      'PhpSoapClient\Command\ListMethodsCommand', $app->get('list-methods'));
  }
}
