jQuery(function ($) {
    // Initialize service connect class for view callbacks
    let serviceConnect = new ServiceConnection(external_services.ajax_post);
    let loader = $('.loader-overlay');

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
        } else {
            $('#authAddForm').length ? $('#authAddForm').remove() : '';
        }
    });

    // Test connection function that sends form data to Ajax controller, then send curl request to check to see whether service accepts or fails
    $('#test-connection').click(function () {
        // remove the configure service section
        $('#configure-service-section').remove();

        // If all elements on the form are validated, then make the URL connection via AJAX to the AJAX controller
        if ($('#add-service').valid()) {

            serviceConnect.connect($('#add-service').serializeArray(), loader).then(function (result) {
                // Call configure service view file with API data keys
                let viewCall = {
                    'action': 'call_view',
                    'view': 'configureService',
                    'class': 'ExternalServices\\Classes\\Models\\Configure_Service',
                    'data': jQuery.parseJSON(result.data)
                };

                serviceConnect.connect(viewCall).then(function (result) {
                    $('#add-service-box').after(result.data);
                });

            });
        }
    });

    // data format dropdown event
    $('#data-format').change(function () {
        // if the selected format is csv, then display parsing options e.g. delimeter, escape character
        if ($(this).val() === 'csv') {
            let viewCall = {
                'action': 'call_view',
                'view': 'csvParseSettings'
            };

            serviceConnect.connect(viewCall, loader).then(function (result) {
                $('#dataFormatField').after(result.data);
            });
        } else {
            $('#csvParseForm').length ? $('#csvParseForm').remove() : '';
        }
    });

    $('#deactivate-external-services').click(function(event) {
        event.preventDefault();
        let targetUrl = $(this).attr("href");
        let modalHtml = '<div title="Keep settings and data?"><p>Would you like to keep all the settings and data?</p></div>';
        //$('#es-deactivate').dialog({modal: true});
        $(modalHtml).dialog({
            title: "Keep settings and Data?",
            modal: true,
            buttons: {
                "Delete": function () {
                    let ajaxCall = {
                       'action': 'delete_data'
                    }

                    serviceConnect.connect(ajaxCall, loader).then(function () {
                        window.location.href = targetUrl;
                    });
                },
                "Keep": function () {
                    window.location.href = targetUrl;
                }
            }
        });
    });
});
