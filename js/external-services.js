jQuery(function ($) {
    // Initialize service connect class for view callbacks
    //let serviceConnect = new ServiceConnection(external_services.ajax_post);
    let serviceConnect = new ServiceConnection();
    let loader = $('.loader-overlay');

    // First of all remove that annoying first level item from the main menu
    $('.toplevel_page_external-services-menu .wp-first-item').remove();


    /*
    // Test connection function that sends form data to Ajax controller, then send curl request to check to see whether service accepts or fails
    $('#test-connection').click(function () {
        // remove the configure service section
        $('#configure-service-section').remove();

        // If all elements on the form are validated, then make the URL connection via AJAX to the AJAX controller
        if ($('#add-service').valid()) {

            serviceConnect.connect($('#add-service').serializeArray(), loader).then(async function (result) {
                // Call configure service view file with API data keys
                let viewCall = {
                    'action': 'call_view',
                    'view': 'add-service/configureService',
                    'class': 'ExternalServices\\Classes\\Blocks\\Configure_Service',
                    'data': jQuery.parseJSON(result.data)
                };

                serviceConnect.connect(viewCall).then(function (result) {
                    $('#add-service-box').after(result.data);
                });
            });
        }
    });
    */
    $('#deactivate-external-services').click(function(event) {
        event.preventDefault();
        let targetUrl = $(this).attr("href");
        let modalHtml = '<div title="Keep settings and data?"><p>Would you like to keep all the settings and data?</p></div>';

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
