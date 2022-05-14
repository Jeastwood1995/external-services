jQuery(function ($) {
    // configure service form
    $('#configure-service').validate({
        rules: {
            cronSchedule: 'required',
        }
    });

    // add service form
    $('#add-service').validate({
        rules: {
            serviceName: 'required',
            serviceURL: {
                required: true,
                url: true
            },
            authType: 'required',
            dataFormat: 'required'
        },
        messages: {
            serviceName: 'Please enter a name for the service',
            serviceURL: {
                required: 'Please enter a valid URL for the connection',
                url: 'Please enter a valid URL'
            },
            authType: 'Please enter an authorization method',
            dataFormat: 'Please select an option'
        }
    });
});