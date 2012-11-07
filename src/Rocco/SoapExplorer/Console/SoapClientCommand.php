<?php

namespace Rocco\SoapExplorer\Console;

use Rocco\Console\Application;
use Rocco\Console\Logger;
use Rocco\SoapExplorer\Soap\SoapClient;


class SoapClientCommand extends Application
{
  const DEFAULT_TIMEOUT = 120;

  protected $remote_service;
  protected $log;

  protected function configure()
  {
    $this->log = new Logger();

    ini_set('default_socket_timeout', self::DEFAULT_TIMEOUT);

    $this->params = array(
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
  }

  protected function execute()
  {
    if (true === $this->has_option('quiet'))
    {
      $this->log->set_level(Logger::ERROR);
    }

    if (true === $this->has_option('help'))
    {
      echo $this->get_help();
      return 3;
    }

    if (false === $this->has_option('endpoint'))
    {
      $this->log->error('You must specify an endpoint');
      return 1;
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

      try
      {
        $endpoint = $this->get_option('endpoint');
        $this->log->info('Discovering wsdl at endpoint: %s', $endpoint);

        ini_set('soap.wsdl_cache_enabled', $cache);
        $this->remote_service = new SoapClient($endpoint, array(
          'trace' => 1,
          'exceptions' => true,
          'connection_timeout' => self::DEFAULT_TIMEOUT,
          'cache_wsdl' => $cache,
        ));
      }
      catch (\Exception $e)
      {
        $this->log->error('Could not initialize endpoint');
        $this->log->error($e->getMessage());

        return 1;
      }
    }

    switch ($this->get_argument(1))
    {
      case 'list':
        return $this->list_methods();
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

  protected function call_method()
  {
    //TODO: refactor this method to use exceptions so state can be cleaned up
    $method = $this->get_argument(2);

    if (false === isset($method))
    {
      $this->log->error('You must specify a method name to call');

      return 1;
    }
    else
    {
      $this->log->info('Calling method %s on the remote', $method);
      $input_xml = $this->read_from_stdin(true);

      if (null === $input_xml)
      {
        $this->log->error('No xml was provided');
        return 1;
      }

      try
      {
        $this->log->info(sprintf('Making the request %s', $method));
        $this->remote_service->_requestData = $input_xml;
        $response = $this->remote_service->$method($input_xml);
        print_r($response);

        return 0;
      }
      catch (Exception $e)
      {
        $this->log->error(sprintf('Error while calling %s on %s', $method, $endpoint));
        $this->log->error($e->getMessage());
        return 1;
      }
    }
  }

  protected function request_method()
  {
    $method = $this->get_argument(2);

    if (false === isset($method))
    {
      $this->log->error('You must specify a method name to generate a request');
      return 1;
    }
    else
    {
      $this->log->info('Generating request for %s on remote', $method);

      $request = $this->remote_service->__getRequestObjectForMethod($method);
      $this->remote_service->__call($method, $request);

      $dom = new \DOMDocument;
      $dom->loadXML($this->remote_service->__getLastRequest());
      $dom->preserveWhiteSpace = false;
      $dom->formatOutput = true;

      $result = $dom->saveXml();
      $result = str_replace($this->remote_service->__get_default_value(), '', $result);
      $result = preg_replace('/^<\?xml *version="1.0" *encoding="UTF-8" *\?>\n/i', '', $result);

      echo $result;

      return 0;
    }
  }

}
