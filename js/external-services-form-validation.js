jQuery(function ($) {

    // Validate add service form
    $('#add-service').validate({
        rules: {
            serviceName: 'required',
            serviceURL: {
                required: true,
                url: true
            },
            authKey: 'required',
            dataFormat: 'required'
        },
        messages: {
            serviceName: 'Please enter a name for the service',
            serviceURL: {
                required: 'Please enter a valid URL for the connection',
                url: 'Please enter a valid URL'
            },
            authKey: 'Please enter a name for the authorization header',
            dataFormat: 'Please select an option'
        }
    });
});