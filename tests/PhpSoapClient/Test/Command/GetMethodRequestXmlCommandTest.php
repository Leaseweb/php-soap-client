<?php

namespace PhpSoapClient\Test\Command;

use PhpSoapClient\Application;
use PhpSoapClient\Command\GetWsdlCommand;
use Symfony\Component\Console\Tester\CommandTester;



class GetMethodRequestXmlCommandTest extends \PHPUnit_Framework_TestCase
{
  /**
   * @expectedException         RuntimeException
   * @expectedExceptionMessage  Not enough arguments.
   */
  public function testExecuteNotEnoughArguments()
  {
    $application = new Application();

    $command = $application->find('request');
    $commandTester = new CommandTester($command);
    $commandTester->execute(array('command' => $command->getName()));
  }

  /**
   * @expectedException         InvalidArgumentException
   * @expectedExceptionMessage  You must specify an endpoint.
   */
  public function testExecuteWithoutEndpoint()
  {
    $application = new Application();

    $command = $application->find('call');
    $commandTester = new CommandTester($command);
    $commandTester->execute(
      array('command' => $command->getName(), 'stakker')
    );
  }
}
