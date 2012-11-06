php-soap-client
===============

A command line application to explore SOAP services


Usage
=====

    Usage: soap_client --endpoint wsdl [--method name] [--quiet]

    OPTIONS

      -h, --help      Print this help message
      -q, --quiet     Surpress any kind of output
      -e, --endpoint  Specify the wsdl to inspect. Alternatively you can
                      set the environment variable SOAP_ENDPOINT
      -m, --method    Specify the method to call on the remote service
                      Alternatively you can set the environment variable
                      SOAP_METHOD

    EXAMPLES

      List all available methods:

        soap_client --endpoint http://example.com/Service.wsdl


Installation
============

To install `soap_client` in `/usr/local/bin` you can checkout the source code and build from there:

    git clone https://github.com/nrocco/php-soap-client.git
    make
    sudo make install
