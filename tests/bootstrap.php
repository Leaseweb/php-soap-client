<?php

error_reporting(E_ALL);

$loader = require __DIR__.'/../src/bootstrap.php';
$loader->add('PhpSoapClient\Test', __DIR__);
