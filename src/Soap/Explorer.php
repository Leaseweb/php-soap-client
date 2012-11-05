<?php

namespace Soap;

// sudo tcpdump -nn -vv -A -s 0 -i eth0 dst or src host xxx.xxx.xxx.xxx and port 80

class Explorer
{
  const DEFAULT_TIMEOUT = 120;

  protected $wsdl;
  protected $soap_client;

  public function __construct($wsdl, $wsdl_cache = WSDL_CACHE_NONE)
  {
    //TODO: add verbose parameter to enable more logging

    ini_set('default_socket_timeout', self::DEFAULT_TIMEOUT);
    ini_set('soap.wsdl_cache_enabled', $wsdl_cache);

    $this->wsdl = $wsdl;
    $this->soap_client = new PimpedSoapClient($wsdl, array(
      'trace' => 1,
      'exceptions' => true,
      'connection_timeout' => self::DEFAULT_TIMEOUT,
      'cache_wsdl' => $wsdl_cache
    ));
  }

  public function list_methods()
  {
    //TODO: refactor this method
    return array_keys($this->soap_client->__getMethods());
  }

  public function call_method($method, $xml)
  {
    //TODO: refactor this method to use exceptions so state can be cleaned up
    $this->soap_client->_requestData = $xml;
    return $this->soap_client->$method($xml);
  }

  public function generate_request_xml($method)
  {
    $request = $this->soap_client->__getRequestObjectForMethod($method);
    $this->soap_client->$method($request);

    $dom = new \DOMDocument;
    $dom->loadXML($this->soap_client->__getLastRequest());
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;

    $result = str_replace($this->soap_client->__get_default_value(), '', $dom->saveXml());

    return preg_replace('/^<\?xml *version="1.0" *encoding="UTF-8" *\?>\n/i', '', $result);
  }

}
