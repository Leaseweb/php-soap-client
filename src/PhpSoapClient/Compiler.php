<?php

namespace PhpSoapClient;

use Symfony\Component\Finder\Finder;

class Compiler
{
    public function compile($pharFile = 'soap_client.phar')
    {
        if (file_exists($pharFile)) {
            unlink($pharFile);
        }

        $phar = new \Phar($pharFile, 0, 'soap_client.phar');
        $phar->setSignatureAlgorithm(\Phar::SHA1);

        $phar->startBuffering();

        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->notName('Compiler.php')
            ->in(__DIR__.'/..')
            ;

        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }

        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->exclude('Tests')
            ->in(__DIR__.'/../../vendor/symfony/console/')
            ;

        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }

        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../vendor/autoload.php'));
        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../vendor/composer/autoload_psr4.php'));
        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../vendor/composer/autoload_namespaces.php'));
        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../vendor/composer/autoload_classmap.php'));
        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../vendor/composer/autoload_real.php'));
        if (file_exists(__DIR__.'/../../vendor/composer/include_paths.php')) {
            $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../vendor/composer/include_paths.php'));
        }
        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../vendor/composer/ClassLoader.php'));
        $this->addBin($phar);

        // Stubs
        $phar->setStub($this->getStub());

        $phar->stopBuffering();

        // disabled for interoperability with systems without gzip ext
        $phar->compressFiles(\Phar::GZ);

        unset($phar);
    }

    private function addFile($phar, $file, $strip = true)
    {
        $path = str_replace(dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR, '', $file->getRealPath());

        $content = file_get_contents($file);
        if ($strip) {
            $content = $this->stripWhitespace($content);
        } elseif ('LICENSE' === basename($file)) {
            $content = "\n".$content."\n";
        }

        $phar->addFromString($path, $content);
    }

    private function addBin($phar)
    {
        $content = file_get_contents(__DIR__.'/../../bin/soap_client');
        $content = preg_replace('{^#!/usr/bin/env php\s*}', '', $content);
        $phar->addFromString('bin/soap_client', $content);
    }

    private function stripWhitespace($source)
    {
        if (!function_exists('token_get_all')) {
            return $source;
        }

        $output = '';
        foreach (token_get_all($source) as $token) {
            if (is_string($token)) {
                $output .= $token;
            } elseif (in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                $output .= str_repeat("\n", substr_count($token[1], "\n"));
            } elseif (T_WHITESPACE === $token[0]) {
                // reduce wide spaces
                $whitespace = preg_replace('{[ \t]+}', ' ', $token[1]);
                // normalize newlines to \n
                $whitespace = preg_replace('{(?:\r\n|\r|\n)}', "\n", $whitespace);
                // trim leading spaces
                $whitespace = preg_replace('{\n +}', "\n", $whitespace);
                $output .= $whitespace;
            } else {
                $output .= $token[1];
            }
        }

        return $output;
    }

    private function getStub()
    {
        return <<<'EOF'
#!/usr/bin/env php
<?php

Phar::mapPhar('soap_client.phar');

require 'phar://soap_client.phar/bin/soap_client';

__HALT_COMPILER();
EOF;
    }
}
