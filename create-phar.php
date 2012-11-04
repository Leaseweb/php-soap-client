#!/usr/bin/env php
<?php

$PHAR_NAME = 'soap_client.phar';

$SRC_DIR   = __DIR__ . DIRECTORY_SEPARATOR . 'src';
$BUILD_DIR = __DIR__ . DIRECTORY_SEPARATOR . 'build';

$PHAR_FILE = $BUILD_DIR . DIRECTORY_SEPARATOR . $PHAR_NAME;

@unlink($PHAR_FILE);
$phar = new Phar($PHAR_FILE, 0, $PHAR_NAME);
$phar->buildFromDirectory($SRC_DIR, '/\.php$/');
// $phar->setStub($phar->createDefaultStub("soap_client.php"));

// copy($src_root . "/config.ini", $build_root . "/config.ini");

$stub = <<<'EOD'
#!/usr/bin/env php
<?php
Phar::interceptFileFuncs();
Phar::mungServer(array('REQUEST_URI', 'PHP_SELF', 'SCRIPT_NAME'));
Phar::WebPhar(null, 'web.php');
include "phar://" . __FILE__ . DIRECTORY_SEPARATOR . "cli.php";
__HALT_COMPILER();
EOD;
$phar->setStub($stub);
