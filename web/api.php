<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;

$app = new Silex\Application();
$app['debug'] = true;
$app->before(function (Request $request) use ($app) {
    $wsdl = $request->get('wsdl', null);
    $app['soapclient'] = new PhpSoapClient\Client\SoapClient($wsdl, array(
      'trace' => 1,
      'exceptions' => true,
      'connection_timeout' => 10,
    ));
});


$app->get('/list-methods', function(Request $request) use ($app) {
    $methods = $app['soapclient']->__getMethods();

    return $app->json(array(
        'methods' => array_keys($methods)
    ));
});


$app->get('/request', function(Request $request) use ($app) {
    $method = $request->get('method', null);
    $xml = $app['soapclient']->__getRequestXmlForMethod($method);

    return $app->json(array(
        'request' => $xml
    ));
});


$app->post('/call', function(Request $request) use ($app) {
    $method = $request->get('method', null);
    $xml = $request->get('request');
    $response = $app['soapclient']->__getResponseXmlForMethod($method, $xml);

    return $app->json(array(
        'response' => $response
    ));
});


$app->run();
