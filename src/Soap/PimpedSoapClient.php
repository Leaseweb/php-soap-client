<?php

namespace Soap;

class PimpedSoapClient extends \SoapClient
{
  protected $_default_value = '%%?%%';

  protected $_structs;
  protected $_methods;
  protected $_dry_run;

  public function __construct($endpoint, $options)
  {
    parent::__construct($endpoint, $options);
    $this->__parseAllStructs();
    $this->__parseAllMethods();
  }

  public function __getStructs()
  {
    return $this->_structs;
  }

  public function __getMethods()
  {
    return $this->_methods;
  }

  public function __get_default_value()
  {
    return $this->_default_value;
  }

  public function __doRequest($request, $location, $action, $version='1')
  {
    if (true === isset($this->_requestData))
    {
      return parent::__doRequest($this->_requestData, $location, $action, $version);
    }
    else
    {
      return '';
    }
  }

  public function __getRequestObjectForMethod($methodName)
  {
    $argument_struct = $this->_methods[$methodName]; 

    return $this->__doRecurseStructs($argument_struct);
  }

  protected function __doRecurseStructs($struct_name)
  {
    if (true === isset($this->_structs[$struct_name]))
    {
      $struct = $this->_structs[$struct_name];

      foreach ($struct as $key => $val)
      {
        $struct[$key] = $this->__doRecurseStructs($val);
      }

      return $struct;
    }
    else
    {
      return $this->__get_default_value();
    }
  }

  protected function __parseAllMethods()
  {
    $this->_methods = array();
    foreach ($this->__getFunctions() as $raw_method)
    {
      //TODO: add support for more then one function argument
      preg_match('/\w+ (?P<method>\w+)\((?P<args>[^ ]*)/', $raw_method, $matches);
      $this->_methods[$matches['method']] = $matches['args'];
    }
  }

  protected function __parseAllStructs()
  {
    $this->_structs = array();
    foreach ($this->__getTypes() as $raw_struct)
    {
      preg_match('/struct (?P<name>\w+) {/', $raw_struct, $matches);
      if (false === isset($matches['name']))
      {
        continue;
      }
      $this->_structs[$matches['name']] = $this->__parseSingleStruct($raw_struct);
      unset($matches);
    }
  }

  protected function __parseSingleStruct($raw_struct)
  {
    $body = array();
    preg_match_all('/(?P<struct>\w+) (?P<property>\w+);/', $raw_struct, $matches);
    foreach ($matches['property'] as $i => $prop)
    {
      $body[$prop] = $matches['struct'][$i];  
    }
    unset($matches);
    return $body;
  }
}
