php-soap-client
===============

A command line application to explore SOAP services


Usage
=====

    php-cli-soap-client                                        version 1.3.2
                                                               Nico Di Rocco

    A command line application to explore SOAP web services

    Usage: src/cli.php --endpoint wsdl [options] [action] [method]


    OPTIONS

        All options below can also be set by specifying environment variables.
        Environment variables are named like the long option's name in uppercase
        and prepending it with SOAP_

        So --endpoint http://example.com/soap.wsdl can also be set like this:

          SOAP_ENDPOINT='http://example.com/soap.wsdl'


        -h, --help       Print this help message.
        -q, --quiet      Surpress any kind of output. This option takes pre-
                         cedence over the `-v` or `--verbose` option.
        -v, --verbose    Output more verbose messages. If `-q` or `--quiet`
                         is specified setting this option has no effect.
        -e, --endpoint   Specify the url to the wsdl of the SOAP webservice
                         to inspect. Alternatively you can
        -c, --cache      Flag to enable caching of the wsdl. By default this
                         is disabled.
        -u, --use-editor This option is only relevant when you use the `call`
                         action. If specified the editor in EDITOR environment
                         variable will be opened up.
        -x, --xml        Output responses in raw xml.


    ACTIONS

        The action to perform. If a action is omitted it defaults to `list`.
        The following actions are supported:

        list             Get a list of available methods to call on the remote.
        wsdl             Outputs the raw wsdl in xml format.
        request <method> Generate an xml formatted SOAP request for the given
                         method and output to stdout.
        call <method>    Call the remote service with the `method` specified
                         and output the reponse to stdout.


    METHOD

        Specify the method to call on the remote service


Installation
============

To install `soap_client` in `/usr/local/bin` you can checkout the source code and build from there:

    git clone https://github.com/nrocco/php-soap-client.git
    cd php-soap-client
    sudo make install
