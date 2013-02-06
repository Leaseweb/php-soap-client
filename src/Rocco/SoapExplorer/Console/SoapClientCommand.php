<?php

namespace Rocco\SoapExplorer\Console;

use Rocco\Console\Application;
use Rocco\Console\Logger;
use Rocco\SoapExplorer\Soap\SoapClient;


class SoapClientCommand extends Application
{
  const HELP = '
php-cli-soap-client                                        version 1.3.2
                                                           Nico Di Rocco

A command line application to explore SOAP web services

Usage: %s --endpoint wsdl [options] [action] [method]


OPTIONS

    All options below can also be set by specifying environment variables.
    Environment variables are named like the long option\'s name in uppercase
    and prepending it with SOAP_

    So --endpoint http://example.com/soap.wsdl can also be set like this:

      SOAP_ENDPOINT=\'http://example.com/soap.wsdl\'


    -h, --help       Print this help message.
    -q, --quiet      Surpress any kind of output. This option takes pre-
                     cedence over the `-v` or `--verbose` option.
    -v, --verbose    Output more verbose messages. If `-q` or `--quiet`
                     is specified setting this option has no effect.
    -e, --endpoint   Specify the url to the wsdl of the SOAP webservice
                     to inspect. Alternatively you can
    -c, --cache      Flag to enable caching of the wsdl. By default this
                     is disabled.
    -u, --use-editor This option is only relevant when you use the `call`
                     action. If specified the editor in EDITOR environment
                     variable will be opened up.
    -x, --xml        Output responses in raw xml.


ACTIONS

    The action to perform. If a action is omitted it defaults to `list`.
    The following actions are supported:

    list             Get a list of available methods to call on the remote.
    wsdl             Outputs the raw wsdl in xml format.
    request <method> Generate an xml formatted SOAP request for the given
                     method and output to stdout.
    call <method>    Call the remote service with the `method` specified
                     and output the reponse to stdout.


METHOD

    Specify the method to call on the remote service
';
  const DEFAULT_TIMEOUT = 120;

  protected $remote_service;
  protected $log;

  protected function configure()
  {
    $this->log = new Logger();
    $this->help_text = self::HELP;

    ini_set('default_socket_timeout', self::DEFAULT_TIMEOUT);

    $this->params = array(
      'quiet' => array(
        'short'   => 'q',
        'long'    => 'quiet',
        'default' => false,
      ),
      'verbose' => array(
        'short'   => 'v',
        'long'    => 'verbose',
      ),
      'help' => array(
        'short' => 'h',
        'long'  => 'help',
      ),
      'endpoint' => array(
        'short' => 'e:',
        'long'  => 'endpoint:',
      ),
      'use-editor' => array(
        'short' => 'u',
        'long'  => 'use-editor',
      ),
      'xml' => array(
        'short' => 'x',
        'long'  => 'xml',
      ),
      'cache' => array(
        'short' => 'c',
        'long' => 'cache',
        'default' => false,
      ),
    );
  }

  protected function execute()
  {
    if (true === $this->has_option('help'))
    {
      echo $this->get_help();
      return 3;
    }

    try
    {
      $this->process_options();

      switch ($this->get_argument(1))
      {
        case 'wsdl':
          return $this->output_wsdl();
          break;

        case 'call':
          return $this->call_method($this->get_argument(2));
          break;

        case 'request':
          return $this->request_method($this->get_argument(2));
          break;

        case 'list':
        default:
          return $this->list_methods();
          break;
      }
    }
    catch (\Exception $e)
    {
      $this->log->error($e->getMessage());
      return 1;
    }

    return 0;
  }

  protected function process_options()
  {
    if (true === $this->has_option('quiet'))
    {
      $this->log->set_level(Logger::ERROR+1);
    }
    elseif (true === $this->has_option('verbose'))
    {
      $this->log->set_level(Logger::DEBUG);
    }

    if (false === $this->has_option('endpoint'))
    {
      throw new \Exception('You must specify an endpoint');
    }

    if (true === $this->has_option('cache'))
    {
      $this->log->debug('Enabling caching of wsdl');
      $cache = WSDL_CACHE_MEMORY;
    }
    else
    {
      $this->log->debug('Wsdls are not being cached.');
      $cache = WSDL_CACHE_NONE;
    }

    $endpoint = $this->get_option('endpoint');
    $this->log->info('Discovering wsdl at endpoint: %s', $endpoint);

    $t1 = microtime(true);

    $this->remote_service = new SoapClient($endpoint, array(
      'trace' => 1,
      'exceptions' => true,
      'connection_timeout' => self::DEFAULT_TIMEOUT,
      'cache_wsdl' => $cache,
    ));

    $this->log->debug('Initializing soap service took %s seconds', microtime(true) - $t1);
    unset($t1);

    return true;
  }

  protected function list_methods()
  {
    $this->log->info('Listing all available methods on the remote.');

    // echo implode(' ', array_keys($this->remote_service->__getMethods()));

    foreach (array_keys($this->remote_service->__getMethods()) as $method)
    {
      echo $method.PHP_EOL;
    }

    return 0;
  }

  protected function output_wsdl()
  {
    echo file_get_contents($this->get_option('endpoint')) . PHP_EOL;

    return 0;
  }

  protected function call_method($method = null)
  {
    if (false === isset($method))
    {
      throw new \Exception('You must specify a method name to call');
    }

    if ($this->has_option('use-editor'))
    {
      $editor = $_SERVER['EDITOR'];
      $empty_request = $this->remote_service->__getRequestXmlForMethod($method);

      $this->log->info('Starting editor: ' . $editor);
      $request_xml = $this->read_from_editor($editor, $empty_request);
    }
    else
    {
      $request_xml = $this->read_from_stdin(true);
    }

    if (null === $request_xml)
    {
      $this->log->info('Create xml request below and finish with ctrl+d');
      $request_xml = $this->read_from_stdin(false);
    }

    try
    {
      $this->log->info('Calling method %s on the remote', $method);
      $t1 = microtime(true);

      if (true === $this->has_option('xml'))
      {
        $response = $this->remote_service->__getResponseXmlForMethod($method, $request_xml);
      }
      else
      {
        $response = $this->remote_service->__getResponseObjectForMethod($method, $request_xml);
      }

      $this->log->info('Calling method took %s seconds', microtime(true) - $t1);
      unset($t1);

      print_r($response);

      return 0;
    }
    catch (Exception $e)
    {
      $this->log->error(sprintf('Error while calling %s on %s', $method, $endpoint));
      throw $e;
    }
  }

  protected function request_method($method = null)
  {
    if (false === isset($method))
    {
      throw new \Exception('You must specify a method name to call');
    }
    else
    {
      $this->log->info('Generating request for %s on remote', $method);
      echo $this->remote_service->__getRequestXmlForMethod($method);

      return 0;
    }
  }

  protected function read_from_editor($editor, $contents=null)
  {
    $temp_file = tmpfile();
    $temp_file_info = stream_get_meta_data($temp_file);

    if (false === is_null($contents))
    {
      fwrite($temp_file, $contents);
    }

    $temp_filename = $temp_file_info['uri'];

    $this->log->debug('Start editing file ' . $temp_filename);

    system("$editor $temp_filename > `tty`");

    $input_xml = $this->read_from_file($temp_filename);
    fclose($temp_file);

    if (0 === strcmp((string)$contents, $input_xml))
    {
      $this->log->debug('File wasn\'t modified');
    }
    else
    {
      $this->log->debug('File was modified using the editor');
    }

    return $input_xml;
  }

  protected function read_from_file($filename, $length=2048)
  {
    $file = fopen($filename, 'r');
    $contents = fread($file, $length);
    fclose($blaat);

    return $contents;
  }
}
