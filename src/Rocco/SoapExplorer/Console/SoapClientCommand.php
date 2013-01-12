<?php

namespace Rocco\SoapExplorer\Console;

use Rocco\Console\Application;
use Rocco\Console\Logger;
use Rocco\SoapExplorer\Soap\SoapClient;


class SoapClientCommand extends Application
{
  const HELP = '
php-cli-soap-client                                          version 1.2
                                                           Nico Di Rocco

A command line application to explore SOAP web services

Usage: %s --endpoint wsdl [options] [action] [method]


OPTIONS

    -h, --help       Print this help message.
    -q, --quiet      Surpress any kind of output. This option takes pre-
                     cedence ofer the `-v` or `--verbose` option.
    -v, --verbose    Output more verbose messages. Only works if `-q` or
                     `--quiet` is not specified.
    -e, --endpoint   Specify the wsdl to inspect. Alternatively you can
                     set the environment variable SOAP_ENDPOINT.
    -c, --cache      Flag to enable caching of the wsdl. By default this
                     is disabled.
    -u, --use-editor This option is only relevant when you use the `call`
                     action. If specified the editor in EDITOR environment
                     variable will be opened up.

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
      'cache' => array(
        'short' => 'c',
        'long' => 'cache',
        'default' => false,
      ),
    );
  }

  protected function execute()
  {
    try
    {
      if (true === $this->has_option('quiet'))
      {
        $this->log->set_level(Logger::ERROR);
      }
      elseif (true === $this->has_option('verbose'))
      {
        $this->log->set_level(Logger::DEBUG);
      }

      if (true === $this->has_option('help'))
      {
        echo $this->get_help();
        return 3;
      }

      if (false === $this->has_option('endpoint'))
      {
        throw new \Exception('You must specify an endpoint');
      }
      else
      {
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

        ini_set('soap.wsdl_cache_enabled', $cache);
        $this->remote_service = new SoapClient($endpoint, array(
          'trace' => 1,
          'exceptions' => true,
          'connection_timeout' => self::DEFAULT_TIMEOUT,
          'cache_wsdl' => $cache,
        ));

        $this->log->debug('Initializing soap service took %s seconds', microtime(true) - $t1);
        unset($t1);
      }

      switch ($this->get_argument(1))
      {
        case 'list':
          return $this->list_methods();
          break;

        case 'wsdl':
          return $this->output_wsdl();
          break;

        case 'call':
          return $this->call_method();
          break;

        case 'request':
          return $this->request_method();
          break;

        default:
          $this->log->error('No valid action provided');
          return 1;
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
    echo file_get_contents($this->get_option('endpoint'));

    return 0;
  }

  protected function call_method()
  {
    $method = $this->get_argument(2);

    if (false === isset($method))
    {
      throw new \Exception('You must specify a method name to call');
    }

    if ($this->has_option('use-editor'))
    {
      $editor = $_SERVER['EDITOR'];
      $empty_request = $this->generate_xml_request($method);

      $this->log->info('Starting editor: ' . $editor);
      $input_xml = $this->read_from_editor($editor, $empty_request);
    }
    else
    {
      $input_xml = $this->read_from_stdin(true);
    }

    if (null === $input_xml)
    {
      $this->log->info('Create xml request below and finish with ctrl+d');
      $input_xml = $this->read_from_stdin(false);
    }

    try
    {
      $this->log->info('Calling method %s on the remote', $method);
      $t1 = microtime(true);
      $this->remote_service->_requestData = $input_xml;
      $response = $this->remote_service->$method($input_xml);
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

  protected function request_method()
  {
    $method = $this->get_argument(2);

    if (false === isset($method))
    {
      throw new \Exception('You must specify a method name to call');
    }
    else
    {
      $this->log->info('Generating request for %s on remote', $method);
      echo $this->generate_xml_request($method);

      return 0;
    }
  }

  protected function generate_xml_request($method)
  {
    $request = $this->remote_service->__getRequestObjectForMethod($method);
    $this->remote_service->__call($method, $request);

    $dom = new \DOMDocument;
    $dom->loadXML($this->remote_service->__getLastRequest());
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;

    $result = $dom->saveXml();
    $result = str_replace($this->remote_service->__get_default_value(), '', $result);
    $result = preg_replace('/^<\?xml *version="1.0" *encoding="UTF-8" *\?>\n/i', '', $result);

    return $result;
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

    fseek($temp_file, 0);
    $input_xml = fread($temp_file, 1024);
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
}
