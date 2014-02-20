<?php

namespace PhpSoapClient\Client;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class SoapClient extends \SoapClient implements LoggerAwareInterface
{
    protected $default_value = '%%?%%';

    protected $logger;

    protected $structs;
    protected $methods;

    public function __construct($endpoint, $options = array())
    {
        if (true === isset($options['cache_wsdl'])) {
            ini_set('soap.wsdl_cache_enabled', $options['cache_wsdl']);
        }

        if (true === isset($options['logger'])) {
            $this->setLogger($options['logger']);
            unset($options['logger']);
        }

        parent::__construct($endpoint, $options);

        $this->__parseAllStructs();
        $this->__parseAllMethods();
    }

    /*
     * Setter for logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __getStructs()
    {
        return $this->structs;
    }

    public function __getMethods()
    {
        return $this->methods;
    }

    public function __getDefaultValue()
    {
        return $this->default_value;
    }

    public function __getRequestXmlForMethod($method)
    {
        $request = $this->__getRequestObjectForMethod($method);
        $this->__call($method, $request);

        $dom = new \DOMDocument();
        $dom->loadXML($this->__getLastRequest());
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $request_xml = $dom->saveXml();
        $request_xml = str_replace($this->__getDefaultValue(), '', $request_xml);
        $request_xml = preg_replace('/^<\?xml *version="1.0" *encoding="UTF-8" *\?>\n/i', '', $request_xml);

        return $request_xml;
    }

    public function __getRequestObjectForMethod($methodName)
    {
        $arguments = $this->methods[$methodName];
        $object = array();

        foreach ($arguments as $struct) {
            $object[] = $this->__doRecurseStructs($struct);
        }

        return $object;
    }

    public function __getResponseXmlForMethod($method, $request_xml)
    {
        /* We can get the XML directly through SoapClient::__getLastResponse() */
        $this->__getResponseObjectForMethod($method, $request_xml);

        $dom = new \DOMDocument();
        $dom->loadXML($this->__getLastResponse());
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        $response_xml = $dom->saveXml();

        return $response_xml;
    }

    public function __getResponseObjectForMethod($method, $request_xml)
    {
        $this->_requestXml = $request_xml;
        $response_object = $this->$method($request_xml);
        $this->_requestXml = null;

        return $response_object;
    }

    public function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        if (true === isset($this->_requestXml)) {
            return parent::__doRequest($this->_requestXml, $location, $action, $version, $one_way);
        } else {
            return '';
        }
    }

    protected function __doRecurseStructs($struct_name)
    {
        if (true === isset($this->structs[$struct_name])) {
            $struct = $this->structs[$struct_name];

            foreach ($struct as $key => $val) {
                $struct[$key] = $this->__doRecurseStructs($val);
            }

            return $struct;
        } else {
            return $this->__getDefaultValue();
        }
    }

    protected function __parseAllMethods()
    {
        $this->methods = array();
        $functions = $this->__getFunctions();

        foreach ($functions as $raw_method) {
            $this->logger->debug('Found method: ' . $raw_method);

            preg_match('/(?P<response>\w+) (?P<method>\w+)\((?P<args>[^\)]*)\)/', $raw_method, $matches);

            foreach (explode(', ', $matches['args']) as $arg) {
                if (true === empty($arg)) {
                    $this->methods[$matches['method']] = array();
                    continue;
                }

                preg_match('/(?P<type>\w+) \$(?P<name>\w+)/', $arg, $matches2);

                $this->methods[$matches['method']][$matches2['name']] = $matches2['type'];

                unset($matches2);
            }
            unset($matches);
        }
    }

    protected function __parseAllStructs()
    {
        $this->structs = array();
        $types = $this->__getTypes();

        if (true === empty($types)) {
            $this->logger->debug('Found 0 structs in WSDL');
        } else {
            $this->logger->debug('Found following structs in WSDL: ' . var_export($types, 1));
        }

        foreach ($types as $raw_struct) {
            try {
                $struct = $this->__parseSingleStruct($raw_struct);
            }
            catch (\RuntimeException $e) {
                $this->logger->warning("Could not parse struct: $raw_struct");
                continue;
            }

            $this->logger->debug("Found struct `" . $struct['name'] . "` with arguments " .var_export($struct['body'], 1));

            $this->structs[$struct['name']] = $struct['body'];
        }
    }

    protected function __parseSingleStruct($raw_struct)
    {
        preg_match('/struct (?P<name>\w+) {/', $raw_struct, $matches);

        if (false === isset($matches['name'])) {
            throw new \RuntimeException('Could not parse struct name');
        }

        $name = $matches['name'];
        $body = array();

        preg_match_all('/(?P<struct>\w+) (?P<property>\w+);/', $raw_struct, $matches2);

        foreach ($matches2['property'] as $i => $prop) {
            $body[$prop] = $matches2['struct'][$i];
        }

        return array(
            'name' => $name,
            'body' => $body
        );
    }
}
