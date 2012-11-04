<?php

namespace Soap;

// sudo tcpdump -nn -vv -A -s 0 -i eth0 dst or src host xxx.xxx.xxx.xxx and port 80

class Explorer
{
  const DEFAULT_TIMEOUT = 120;

  protected $wsdl;
  protected $_soap_client;

  public function __construct($wsdl)
  {
    //TODO: add verbose parameter to enable more logging

    ini_set('default_socket_timeout', self::DEFAULT_TIMEOUT);
    ini_set('soap.wsdl_cache_enabled', 0);

    $this->wsdl = $wsdl;
    $this->soap_client = new \SoapClient($wsdl, array(
      'trace' => 1,
      'exceptions' => true,
      'connection_timeout' => self::DEFAULT_TIMEOUT,
      'cache_wsdl' => WSDL_CACHE_NONE
    ));
  }

  public function list_methods()
  {
    //TODO: implement this method
  }

  public function call_method($method, $args=null)
  {
    //TODO: implement this method
    return $this->soap_client->$method($args);
  }

}
