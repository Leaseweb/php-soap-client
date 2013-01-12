php-soap-client
===============

A command line application to explore SOAP services


Usage
=====

    php-cli-soap-client                                          version 1.2
                                                               Nico Di Rocco

    A command line application to explore SOAP web services

    Usage: build/soap_client.phar --endpoint wsdl [options] [action] [method]


    OPTIONS

        -h, --help       Print this help message.
        -q, --quiet      Surpress any kind of output. This option takes pre-
                         cedence ofer the `-v` or `--verbose` option.
        -v, --verbose    Output more verbose messages. Only works if `-q` or
                         `--quiet` is not specified.
        -e, --endpoint   Specify the wsdl to inspect. Alternatively you can
                         set the environment variable SOAP_ENDPOINT.
        -c, --cache      Flag to enable caching of the wsdl. By default this
                         is disabled.
        -u, --use-editor This option is only relevant when you use the `call`
                         action. If specified the editor in EDITOR environment
                         variable will be opened up.

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
    make
    sudo make install
