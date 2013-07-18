<?php

namespace Rocco\PhpSoapClient;

use Symfony\Component\Console\Application as BaseApplication;


class SoapClientApplication extends BaseApplication
{
  const NAME = 'php-soap-client';
  const VERSION = '1.3.0';

  public function __construct()
  {
    parent::__construct(static::NAME, static::VERSION);
  }
}
