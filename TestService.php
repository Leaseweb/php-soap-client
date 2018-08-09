<?php

require_once __DIR__.'/vendor/autoload.php';

ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache

$URI = 'http://127.0.0.1:7999/TestService.php';



/**
 * Class TestService
 *
 * @author Nico Di Rocco <dirocco.nico@gmail.com>
 */
class TestService
{
    /**
     * SayHello
     *
     * @param $name
     * @return string
     */
    public function SayHello($name)
    {
        return sprintf("Hello %s", $name);
    }

    /**
     * GetCurrentDate
     *
     * @return string
     */
    public function GetCurrentDate()
    {
        return time();
    }

    /**
     * CalculateMD5
     *
     * @param string $message
     * @return
     */
    public function CalculateMD5($message)
    {
        return array(
            'message' => $message,
            'md5hash' => md5($message)
        );
    }
}


if (isset($_GET['wsdl'])) {
    $wsdl = new \WSDL\WSDLCreator('TestService', $URI);
    $wsdl->setNamespace("http://example.com/");
    $wsdl->renderWSDL();
    exit;
}

$server = new SoapServer(null, array('uri' => $URI));
$server->setClass("TestService");
$server->handle();
