#!/usr/bin/env php
<?php

require __DIR__.'/src/bootstrap.php';

use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;


error_reporting(-1);
ini_set('display_errors', 1);


function getStub()
{
  $stub = <<<'EOF'
#!/usr/bin/env php
Phar::mapPhar('soap_client.phar');
EOF;

        return $stub . <<<'EOF'
require 'phar://soap_client.phar/src/cli.php';

__HALT_COMPILER();
EOF;
  return $stub;
}

function addFile($phar, $file, $strip = false)
{
  $path = str_replace(dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR, '', $file->getRealPath());

  $content = file_get_contents($file);
  // if ($strip) {
  //   $content = $this->stripWhitespace($content);
  // } elseif ('LICENSE' === basename($file)) {
  //   $content = "\n".$content."\n";
  // }

  $phar->addFromString($path, $content);
}

function compile()
{
  $PHAR_NAME = 'soap_client';
  $SRC_DIR   = __DIR__ . DIRECTORY_SEPARATOR . 'src';
  $VENDOR_DIR= __DIR__ . DIRECTORY_SEPARATOR . 'vendor';
  $BUILD_DIR = __DIR__ . DIRECTORY_SEPARATOR . 'build';
  $PHAR_FILE = $BUILD_DIR . DIRECTORY_SEPARATOR . $PHAR_NAME . '.phar';

  if (file_exists($PHAR_FILE))
  {
    unlink($PHAR_FILE);
  }

  $phar = new \Phar($PHAR_FILE, 0, 'soap-client.phar');
  $phar->setSignatureAlgorithm(\Phar::SHA1);

  $phar->startBuffering();

  $finder = new Finder();
  $finder->files()
    ->ignoreVCS(true)
    ->name('*.php')
    ->in(__DIR__.'/src')
    ;

  foreach ($finder as $file)
  {
    addFile($phar, $file);
  }

  $finder = new Finder();
  $finder->files()
    ->ignoreVCS(true)
    ->name('*.php')
    ->exclude('Tests')
    ->in(__DIR__.'/vendor/symfony/')
    ;

  foreach ($finder as $file)
  {
    addFile($phar, $file);
  }

  addFile($phar, new \SplFileInfo(__DIR__.'/vendor/autoload.php'));
  addFile($phar, new \SplFileInfo(__DIR__.'/vendor/composer/autoload_namespaces.php'));
  addFile($phar, new \SplFileInfo(__DIR__.'/vendor/composer/autoload_classmap.php'));
  addFile($phar, new \SplFileInfo(__DIR__.'/vendor/composer/autoload_real.php'));
  if (file_exists(__DIR__.'/vendor/composer/include_paths.php'))
  {
    addFile($phar, new \SplFileInfo(__DIR__.'/vendor/composer/include_paths.php'));
  }
  addFile($phar, new \SplFileInfo(__DIR__.'/vendor/composer/ClassLoader.php'));

  // Stubs
  $phar->setStub(getStub());
  $phar->stopBuffering();

  unset($phar);
}

try
{
  compile();
}
catch (\Exception $e)
{
    echo 'Failed to compile phar: ['.get_class($e).'] '.$e->getMessage().' at '.$e->getFile().':'.$e->getLine();
    exit(1);
}
