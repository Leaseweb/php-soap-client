<?php

require_once 'autoloader.php';

$HELP = 'Usage: %s --endpoint wsdl [--method name] [--quiet]

  OPTIONS

    -h, --help      Print this help message
    -q, --quiet     Surpress any kind of output
    -e, --endpoint  Specify the wsdl to inspect. Alternatively you can
                    set the environment variable SOAP_ENDPOINT
    -m, --method    Specify the method to call on the remote service
                    Alternatively you can set the environment variable
                    SOAP_METHOD

  EXAMPLES

    List all available methods:

      soap_client --endpoint http://example.com/Service.wsdl 

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
  $log->set_level(Cli\Logger::ERROR);
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
  $log->error('You must specify an endpoint');
  exit(1);
}

try
{
  $log->info('Discovering wsdl at endpoint: %s', $endpoint);
  $remote_service = new Soap\Explorer($endpoint, WSDL_CACHE_MEMORY);
}
catch (\Exception $e)
{
  $log->error('Could not initialize endpoint');
  $log->error($e->getMessage());
  exit(1);
}

if (true === empty($method))
{
  $log->info('No method provided. Listing all methods:');
  foreach ($remote_service->list_methods() as $method)
  {
    echo $method.PHP_EOL;
  }
}
else
{
  $log->info('Calling method %s', $method);

  $input_xml = $app->read_from_stdin(true);

  if (null === $input_xml)
  {
    $log->info('No input was provided. Generating a sample request object.');
    $request_xml = $remote_service->generate_request_xml($method);
    echo $request_xml;
  }
  else
  {
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
}

exit(0);
