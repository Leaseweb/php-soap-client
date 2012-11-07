php-soap-client
===============

A command line application to explore SOAP services


Usage
=====

    Usage: soap_client --endpoint wsdl [--quiet] [--cache] [action] [method]

      OPTIONS

        -h, --help      Print this help message
        -q, --quiet     Surpress any kind of output
        -e, --endpoint  Specify the wsdl to inspect. Alternatively you can
                        set the environment variable SOAP_ENDPOINT
        -c, --cache     Flag to enable caching of the wsdl. By default this is
                        disabled.

      ARGUMENTS

        [action]        The action to perform, can be either one of:
                         - list
                         - request <method>
                         - call <method>
                        If action is not given, it defaults to list.
        [method]        Specify the method to call on the remote service


      EXAMPLES

        List all available methods:

          soap_client --endpoint http://example.com/Service.wsdl

        Call a method on the remote service

          soap_client --endpoint http://example.com/Service.wsdl call CalculatePrice

        Generate a sample request for the CalculatePrice method in xml format

          soap_client --endpoint http://example.com/Service.wsdl request CalculatePrice



Installation
============

To install `soap_client` in `/usr/local/bin` you can checkout the source code and build from there:

    git clone https://github.com/nrocco/php-soap-client.git
    make
    sudo make install
