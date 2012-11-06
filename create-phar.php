#!/usr/bin/env php
<?php

$PHAR_NAME = 'soap_client';

$SRC_DIR   = __DIR__ . DIRECTORY_SEPARATOR . 'src';
$BUILD_DIR = __DIR__ . DIRECTORY_SEPARATOR . 'build';

$PHAR_FILE = $BUILD_DIR . DIRECTORY_SEPARATOR . $PHAR_NAME . '.phar';

$phar = new Phar($PHAR_FILE, 0, $PHAR_NAME);
// $phar->buildFromDirectory($SRC_DIR, '/\.php$/');

$oDir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($SRC_DIR), RecursiveIteratorIterator::SELF_FIRST);
foreach ($oDir as $sFile)
{
  if (preg_match('/\\.php$/i', $sFile))
  {
    $phar->addFromString(substr($sFile, strlen($SRC_DIR)+1), php_strip_whitespace($sFile));
  }
}

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
