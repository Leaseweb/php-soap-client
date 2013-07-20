php-soap-client
===============

A command line application to explore SOAP services


Usage
=====

    php-soap-client version 2.0.0

    Usage:
      [options] command [arguments]

    Options:
      --help           -h Display this help message.
      --quiet          -q Do not output any message.
      --verbose        -v|vv|vvv Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
      --version        -V Display this application version.
      --ansi              Force ANSI output.
      --no-ansi           Disable ANSI output.
      --no-interaction -n Do not ask any interactive question.

    Available commands:
      call           Call the remote service with the `method` specified and output the reponse to stdout.
      help           Displays help for a command
      list           Lists commands
      list-methods   Get a list of available methods to call on the remote.
      request        Generate an xml formatted SOAP request for the given method and output to stdout.
      wsdl           Get the WSDL of a soap service.



Installation
============

*Easy method (no sudo):*

Download the latest `soap_client.phar` and start using it immediatly:

    wget http://nrocco.github.io/php-soap-client/soap_client.phar
    chmod +x soap_client.phar
    soap_client.phar --help


*Difficult method (needs sudo):*

To install `soap_client` in `/usr/bin` you can checkout the source code and build from there:

    git clone https://github.com/nrocco/php-soap-client.git
    cd php-soap-client
    sudo make install
    soap_client --help
