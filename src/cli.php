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
  'method' => array(
    'short' => 'm:',
    'long'  => 'method:',
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


$endpoint = $app->get_option('endpoint');
$method = $app->get_option('method');

if (true === empty($endpoint))
{
  $log->error('ERROR: You must specify an endpoint');
  exit(1);
}

try
{
  $log->info('Discovering wsdl at endpoint: %s', $endpoint);
  $remote_service = new Soap\Explorer($endpoint);
}
catch (\Exception $e)
{
  $log->error('ERROR: Could not initialize endpoint');
  exit(1);
}

if (true === empty($method))
{
  $log->info('No method provided. Listing all methods:');
  foreach ($remote_service->list_methods() as $method)
  {
    $log->info(' - %s', $method);
  }
}
else
{
  $log->info('Calling method %s', $method);
  $remote_service->call_method($method, array());
}


exit(0);

