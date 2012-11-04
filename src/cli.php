<?php

require_once 'autoloader.php';

$HELP = 'Usage: %s <method>

OPTIONS

  -h, --help      Print this help message
  -q, --quiet     Surpress any kind of output
  -e, --endpoint  Specify the wsdl to inspect. Alternatively you can
                  set the environment variable SOAP_ENDPOINT

ARGUMENTS

  method          The method to call on the endpoint

';

$PARAMS = array(
  'quiet' => array(
    'short'   => 'q',
    'long'    => 'quiet',
    'default' => false,
  ),
  'help' => array(
    'short' => 'h',
    'long'  => 'help',
  ),
  'endpoint' => array(
    'short' => 'e:',
    'long'  => 'endpoint:',
  ),
);


$app = new Cli\Application($_SERVER['argv'][0]);
$app->set_help($HELP);
$app->set_params($PARAMS);
$app->parse_options();

$log = new Cli\Logger();
if (true === $app->has_option('quiet'))
{
  $log->set_level(Logger::ERROR);
}

if (true === $app->has_option('help'))
{
  echo $app->get_help();
  exit(3);
}



if (false === $app->has_option('endpoint') && true === isset($_SERVER['SOAP_ENDPOINT']))
{
  $app->set_option('endpoint', $_SERVER['SOAP_ENDPOINT']);
}

$endpoint = $app->get_option('endpoint');

if (true === empty($endpoint))
{
  $log->error('ERROR: you must specify and endpoint');
  exit(1);
}

$log->info('Using endpoint: %s', $endpoint);
exit(0);

