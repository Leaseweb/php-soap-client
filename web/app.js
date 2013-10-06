function isValidUrl(url) {
    if(/^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/|www\.)[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/.test(url)) {
        return true;
    }
    else {
        return false;
    }
}


jQuery(document).ready(function($) {

    function parse_wsdl_handler(event) {
        var wsdl = $('#wsdl_endpoint').val();

        if (isValidUrl(wsdl)) {
            $.getJSON('api.php/list-methods', {wsdl:wsdl}, function(data){
                $('#wsdl_endpoint').prop('disabled', true);
                $('#parse_wsdl').css('display', 'none');
                $('#reset_wsdl').css('display', 'inherit');

                var output = [
                    '<option value="">Select a method</option>'
                ];
                $.each(data.methods, function(key, val) {
                    output.push('<option value="'+val+'">'+val+'</option>');
                });
                $('#method-list').html(output.join(''));
            });
        }
        else {
            alert(wsdl + ' is not a valid url');
        }
    }

    function reset_wsdl_handler(event) {
        method_list_change_handler(event);

        $('#wsdl_endpoint').prop('disabled', false);
        $('#wsdl_endpoint').val('');

        $('#reset_wsdl').css('display', 'none');
        $('#parse_wsdl').css('display', 'inherit');

        $('#method-list').html('');
    }

    function get_request_handler(event) {
        var wsdl = $('#wsdl_endpoint').val();
        var method = $('#method-list :selected').text();

        $.getJSON('api.php/request', {wsdl:wsdl, method:method}, function(data) {
            $('#reqresp').css('display', 'inline');
            $('#request code').text(data.request);
        });
    }

    function method_list_change_handler(event) {
        $('#reqresp').css('display', 'none');

        $('#request code').text('');
        $('#response code').text('');
    }


    function call_method_handler(event) {
        var wsdl = $('#wsdl_endpoint').val();
        var method = $('#method-list :selected').text();
        var request = $('#request code').text();

        $('#response code').text('');

        $.post('api.php/call', {
            wsdl: wsdl,
            method: method,
            request: request
        }, function(data) {
            $('#response code').text(data.response);
        }, 'json');
    }

    $('#parse_wsdl').click(parse_wsdl_handler);
    $('#reset_wsdl').click(reset_wsdl_handler);
    $('#get-request').click(get_request_handler);
    $('#call-method').click(call_method_handler);
    $('#method-list').change(method_list_change_handler);
});
