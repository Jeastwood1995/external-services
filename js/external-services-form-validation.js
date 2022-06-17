jQuery(function ($) {
    // add service form
    $('#add-service').validate({
        rules: {
            serviceName: 'required',
            serviceUrl: {
                required: true,
                url: true
            },
            authType: 'required',
            dataFormat: 'required'
        },
        messages: {
            serviceName: 'Please enter a name for the service',
            serviceUrl: {
                required: 'Please enter a valid URL for the connection',
                url: 'Please enter a valid URL'
            },
            authType: 'Please enter an authorization method',
            dataFormat: 'Please select an option'
        }
    });
});