<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use Rocco\PhpSoapClient\Command\GetWsdlCommand;
use Rocco\PhpSoapClient\SoapClientApplication;
use Rocco\PhpSoapClient\Command\CallMethodCommand;
use Rocco\PhpSoapClient\Command\GetMethodRequestXmlCommand;
use Rocco\PhpSoapClient\Command\ListMethodsCommand;


$console = new SoapClientApplication();
$console->add(new GetWsdlCommand);
$console->add(new CallMethodCommand);
$console->add(new GetMethodRequestXmlCommand);
$console->add(new ListMethodsCommand);
$console->run();
