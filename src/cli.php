<?php

require_once 'autoloader.php';

$app = new Rocco\SoapExplorer\Console\SoapClientCommand($_SERVER['argv'][0]);
$retval = $app->bootstrap();

exit($retval);
