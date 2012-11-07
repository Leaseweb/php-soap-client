<?php

namespace Rocco\SoapExplorer\Soap;

class SoapClient extends \SoapClient
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
    $arguments = $this->_methods[$methodName]; 
    $object = array();

    foreach ($arguments as $arg => $struct)
    {
      $object[] = $this->__doRecurseStructs($struct);
    }

    return $object;
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
      preg_match('/(?P<response>\w+) (?P<method>\w+)\((?P<args>[^\)]+)/', $raw_method, $matches);

      foreach (explode(', ', $matches['args']) as $arg)
      {
        preg_match('/(?P<type>\w+) \$(?P<name>\w+)/', $arg, $matches2);
        $this->_methods[$matches['method']][$matches2['name']] = $matches2['type'];
        unset($matches2);
      }
      unset($matches);
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
