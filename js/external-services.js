jQuery(function ($) {
    // Initialize service connect class for view callbacks
    let serviceConnect = new ServiceConnection(external_services.ajax_post);

    // First of all remove that annoying first level item from the main menu
    $('.toplevel_page_external-services-menu .wp-first-item').remove();

    // Show authorization field if keycheck is checked
    $('#auth-header').change(function () {

        let viewCall = {
            'action': 'call_view',
            'view': 'authHeaderConfigure'
        };

        if ($(this).is(':checked')) {
            serviceConnect.connect(viewCall).then(function (result) {
                $('#authHeadField').after(result.data);
            });
        }
        /*
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
        */
    });

    // Test connection function that sends form data to Ajax controller, then send curl request to check to see whether service accepts or fails
    $('#test-connection').click(function () {
        // remove the configure service section
        $('#configure-service-section').remove();

        // If all elements on the form are validated, then make the URL connection via AJAX to the AJAX controller
        if ($('#add-service').valid()) {

            serviceConnect.connect($('#add-service').serializeArray(), $('.loader-overlay')).then(function (result) {
                // Call configure service view file with API data keys
                let viewCall = {
                    'action': 'call_view',
                    'view': 'configureService',
                    'class': 'ExternalServices\\Classes\\Controllers\\Configure_Service',
                    'data': jQuery.parseJSON(result.data)
                };

                serviceConnect.connect(viewCall).then(function (result) {
                    $('#add-service-box').after(result.data);
                });

            });
        }
    });

    // Mass check/uncheck all mapping checkboxes
    $(document).on('change', '#all-keys', function () {
        let mappingCheckboxes = $('#configure-service').find('.data-mappings');

        ($(this).is(':checked')) ? $(mappingCheckboxes).prop('checked', true) : $(mappingCheckboxes).prop('checked', false);
    });
});
