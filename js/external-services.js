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
        let serviceConnect = new ServiceConnection(external_services.ajax_post);

        if ($('#add-service').valid()) {
            let data = serviceConnect.connect($('#add-service').serializeArray(), $('.loader-overlay'));

            if (data !== '') {
                let viewCall = {
                    'action': 'call_view',
                    'view': 'configureService',
                    'data': data
                };

                serviceConnect.connect(viewCall);
            }
        }
    });
});