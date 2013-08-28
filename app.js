function isValidUrl(url)
{
    if(/^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/|www\.)[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/.test(url))
    {
        return true;
    }
    else
    {
        return false;
    }
}


jQuery(document).ready(function($)
{
    function parse_wsdl_form_submit_handler(event)
    {
        event.preventDefault;

        var wsdl = $('#wsdl_endpoint').val();

        if (isValidUrl(wsdl))
        {
            console.log(wsdl);
        }
        else 
        {
            alert(wsdl + ' is not a valid url');
        }
        return false;
    }

    $('#parse_wsdl').submit(parse_wsdl_form_submit_handler);
});
