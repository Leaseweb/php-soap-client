<?php

require_once 'autoloader.php';

$HELP = 'Usage: %s --endpoint wsdl [--quiet] [--cache] [action] [method]

Version 1.1

  OPTIONS

    -h, --help      Print this help message
    -q, --quiet     Surpress any kind of output
    -e, --endpoint  Specify the wsdl to inspect. Alternatively you can
                    set the environment variable SOAP_ENDPOINT
    -c, --cache     Flag to enable caching of the wsdl. By default this is
                    disabled.

  ARGUMENTS

    [action]        The action to perform, can be either one of:
                     - list
                     - request <method>
                     - call <method>
                    If action is not given, it defaults to list.
    [method]        Specify the method to call on the remote service

';

$app = new Rocco\SoapExplorer\Console\SoapClientCommand($_SERVER['argv'][0]);
$app->set_help($HELP);
$retval = $app->bootstrap();

exit($retval);
