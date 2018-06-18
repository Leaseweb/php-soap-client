#compdef soap_client

_soap_client () {
    local curcontext="$curcontext" state line
    typeset -A opt_args

    local -a soap_client_commands
    soap_client_commands=(
        'call:Call the remote service with the `method` specified and output the reponse to stdout.'
        'list-methods:Get a list of available methods to call on the remote.'
        'request:Generate an xml formatted SOAP request for the given method and output to stdout.'
        'wsdl:Get the WSDL of a soap service.'
    )

    _arguments -C \
        '--config=[The location to the configuration file]:filename:_files' \
        '--endpoint=[Specify the url to the wsdl of the SOAP webservice to inspect]:args' \
        '--proxy=[Use this proxy to connect to the SOAP web service]:args' \
        '--quiet[Do not output any message]' \
        '--cache[Enable caching of the wsdl]' \
        '1: :{_describe "commands" soap_client_commands}' \
        '*::args:->args'

    case $state in
        (args)
            case $line[1] in
                (call)
                    _soap_client-call
                ;;
                (request)
                    _soap_client-request
                ;;
            esac
        ;;
    esac
}

_soap_client-call () {
    local curcontext="$curcontext" state line
    typeset -A opt_args

    _arguments -C \
        "--editor[Open the request xml in your favorite $EDITOR before sending to the server]" \
        "--xml[Output the results as xml. Otherwise output as an object]" \
        '1: :_soap_client-methods'
}

_soap_client-request () {
    local curcontext="$curcontext" state line
    typeset -A opt_args

    _arguments -C \
        '1: :_soap_client-methods'
}

_soap_client-methods () {
    local endpoint

    for token in $tokens; do
        if [[ "$token" =~ ^--endpoint=.* ]]
        then
            endpoint=$token
            break
        fi
    done

    [[ -z $endpoint ]] && return 1

    local -a soap_client_methods
    soap_client_methods=($($name --cache ${endpoint//\"/} list-methods))
    _describe "methods" soap_client_methods
}

_soap_client "$@"
