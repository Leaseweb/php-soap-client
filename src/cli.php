<?php

require_once 'autoloader.php';

$HELP = 'Usage: %s --endpoint wsdl [--quiet] [--cache] [action] [method]

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


  EXAMPLES

    List all available methods:

      soap_client --endpoint http://example.com/Service.wsdl

      soap_client --endpoint http://example.com/Service.wsdl call CalculatePrice

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
  'cache' => array(
    'short' => 'c',
    'long' => 'cache',
    'default' => false,
  ),
);

$app = new Cli\Application($_SERVER['argv'][0]);
$app->set_help($HELP);
$app->set_params($PARAMS);
$app->parse_options();
$app->parse_arguments();

$log = new Cli\Logger();

if (true === $app->has_option('quiet'))
{
  $log->set_level(Cli\Logger::ERROR);
}

if (true === $app->has_option('help'))
{
  echo $app->get_help();
  exit(3);
}


$endpoint = $app->get_option('endpoint');

if (true === empty($endpoint))
{
  $log->error('You must specify an endpoint');
  exit(1);
}

try
{
  $log->info('Discovering wsdl at endpoint: %s', $endpoint);
  if (true === $app->has_option('cache'))
  {
    $log->debug('Enabling caching of wsdl');
    $cache = WSDL_CACHE_MEMORY;
  }
  else
  {
    $log->debug('Wsdls are not being cached.');
    $cache = WSDL_CACHE_NONE;
  }

  $remote_service = new Soap\Explorer($endpoint, $cache);
}
catch (\Exception $e)
{
  $log->error('Could not initialize endpoint');
  $log->error($e->getMessage());
  exit(1);
}

switch ($app->get_argument(1, 'list'))
{
  case 'call':
    $method = $app->get_argument(2);
    if (false === isset($method))
    {
      $log->error('You must specify a method name to call');
      exit(1);
    }
    else
    {
      $log->info('Calling method %s on the remote', $method);
      $input_xml = $app->read_from_stdin(true);

      if (null === $input_xml)
      {
        $log->error('No xml was provided');
        exit(1);
      }

      try
      {
        $log->info(sprintf('Making the request %s', $method));
        $response = $remote_service->call_method($method, $input_xml);
        print_r($response);
      }
      catch (Exception $e)
      {
        $log->error(sprintf('Error while calling %s on %s', $method, $endpoint));
        $log->error($e->getMessage());
        exit(1);
      }
    }
    break;

  case 'request':
    $method = $app->get_argument(2);
    if (false === isset($method))
    {
      $log->error('You must specify a method name to generate a request');
      exit(1);
    }
    else
    {
      $log->info('Generating request for %s on remote', $method);
      $request_xml = $remote_service->generate_request_xml($method);
      echo $request_xml;
    }
    break;

  case 'list':
  default:
    $log->info('Listing all available methods on the remote.');
    foreach ($remote_service->list_methods() as $method)
    {
      echo $method.PHP_EOL;
    }
    break;
}

exit(0);
