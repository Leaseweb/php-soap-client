<?php

namespace App\Test\Client;

use App\Client\SoapClient;
use PHPUnit\Framework\TestCase;

class SoapClientTest extends TestCase
{
    public function testSoapClient()
    {
        $client = new SoapClient(
      'http://www.w3schools.com/webservices/tempconvert.asmx?WSDL'
    );

        $expected_methods = array(
      'FahrenheitToCelsius' => array(
        'parameters' => 'FahrenheitToCelsius',
      ),
      'CelsiusToFahrenheit' => array(
        'parameters' => 'CelsiusToFahrenheit',
      ),
    );

        $expected_structs = array(
      'FahrenheitToCelsius' => array(
        'Fahrenheit' => 'string',
      ),
      'FahrenheitToCelsiusResponse' => array(
        'FahrenheitToCelsiusResult' => 'string',
      ),
      'CelsiusToFahrenheit' => array(
        'Celsius' => 'string',
      ),
      'CelsiusToFahrenheitResponse' => array(
        'CelsiusToFahrenheitResult' => 'string',
      ),
    );

        $this->assertEquals('%%?%%', $client->__getDefaultValue());
        $this->assertEquals($expected_methods, $client->__getMethods());
        $this->assertEquals($expected_structs, $client->__getStructs());

        $xml = '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://tempuri.org/">
  <SOAP-ENV:Body>
    <ns1:FahrenheitToCelsius>
      <ns1:Fahrenheit>23</ns1:Fahrenheit>
    </ns1:FahrenheitToCelsius>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>';

        // $response = $client->__getResponseXmlForMethod('FahrenheitToCelsius', $xml);
        // $this->assertRegExp('/soap:Envelope/', $response);
        // $this->assertRegExp('/soap:Body/', $response);
        // $this->assertRegExp('/FahrenheitToCelsiusResponse/', $response);
        // $this->assertRegExp('/<FahrenheitToCelsiusResult>-5</FahrenheitToCelsiusResult>/', $response);

        $response = $client->__getResponseObjectForMethod('FahrenheitToCelsius', $xml);
        $this->assertEquals('-5', $response->FahrenheitToCelsiusResult);
    }
}
