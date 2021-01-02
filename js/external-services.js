jQuery(function ($) {

    // First of all remove that annoying first level item from the main menu
    $('.toplevel_page_external-services-menu .wp-first-item').remove();

    // Show authorization field if keycheck is checked
    $('#keyCheck').change(function() {
        if ($('#keyCheck').is(':checked')) {
            let authAdd = $('<div id="authAddForm">' +
                '<p id="authKey">' +
                '<label id="authLabel" for="authKey">Authorization Key: </label>' +
                '<input type="text" name="authKey">' +
                '</p>' +
                '</div>');
            $(authAdd).insertAfter('#keyCheckField');
        } else {
            if ($('#authAddForm').length) {
                $('#authAddForm').remove();
            }
        }
    });

    // Test connection function that sends form data to Ajax controller, then send curl request to check to see whether service accepts or fails
    $('#test-connection').click(function (){
        if ($('#add-service').valid()) {
            let form = $('#add-service').serializeArray();
            let loader = jQuery('.loader-overlay');

            $.ajax({
                type: "POST",
                url: external_services.ajax_post,
                data: form ,
                beforeSend: function() {
                    loader.css('display', 'flex');
                },
                success: function (response) {
                    alert('hello');
                },
                error: function (xhr, status, error) {
                    let message = $.parseJSON(xhr.responseText);
                    alert('Error during connection to the service. Error Message: \n\n' + message.data);
                },
                fail: function(xhr, textStatus, errorThrown) {
                    debugger;
                },
                complete: function() {
                    loader.css('display', 'none');
                }
            });
        }
        /*
        let form = $('#add-service').serializeArray();

        $.ajax({
            type: "POST",
            url: external_services.ajax_post,
            data: form ,
            success: function (response) {
                console.log(response);
            },
            error: function (xhr, status, error) {
                let message = xhr.responseText;
                debugger;
                alert('Error during connection to the service. Error Message: ' + message);
            }
        });
         */
    });

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

    /*
    // Get add form data
    $('#add-service').submit(function (e) {
        e.preventDefault();

        let form = $(this);
        let url = form.attr('action');

        $.ajax({
            type: "POST",
            url: url,
            data: {
             form: form.serialize(),
            },
            success: function (response) {
                $(response.data).insertAfter('#wptitle');
            },
            error : function(error){
                console.log(error)
            }
        });

    });
     */
});