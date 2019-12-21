<?php

namespace App\Client;

use PHPUnit\Framework\TestCase;
use Psr\Log\Test\TestLogger;

class SoapClientTest extends TestCase
{
    public function testSoapClient()
    {
        $client = new SoapClient(
            'http://www.dataaccess.com/webservicesserver/numberconversion.wso?WSDL',
            ['logger' => new TestLogger()]
        );

        $expected_methods = [
            'NumberToWords' => [
                'parameters' => 'NumberToWords',
            ],
            'NumberToDollars' => [
                'parameters' => 'NumberToDollars',
            ],
        ];
        $expected_structs = [
            'NumberToWords' => [
                'ubiNum' => 'unsignedLong',
            ],
            'NumberToWordsResponse' => [
                'NumberToWordsResult' => 'string',
            ],
            'NumberToDollars' => [
                'dNum' => 'decimal',
            ],
            'NumberToDollarsResponse' => [
                'NumberToDollarsResult' => 'string',
            ],
        ];

        $this->assertEquals('%%?%%', $client->__getDefaultValue());
        $this->assertEquals($expected_methods, $client->__getMethods());
        $this->assertEquals($expected_structs, $client->__getStructs());

        $xml = '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://www.dataaccess.com/webservicesserver/">
            <SOAP-ENV:Body>
            <ns1:NumberToWords>
            <ns1:ubiNum>12</ns1:ubiNum>
            </ns1:NumberToWords>
            </SOAP-ENV:Body>
            </SOAP-ENV:Envelope>';

        $response = $client->__getResponseObjectForMethod('NumberToWords', $xml);
        $this->assertEquals('twelve ', $response->NumberToWordsResult);
    }
}
