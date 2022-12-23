jQuery(function ($) {
    // Initialize service connect class for view callbacks
    let serviceConnect = new ServiceConnection();

    // First of all remove that annoying first level item from the main menu
    $('.toplevel_page_external-services-menu .wp-first-item').remove();

    $('#deactivate-external-services').click(function(event) {
        event.preventDefault();
        let targetUrl = $(this).attr("href");
        let modalHtml = '<div title="Keep settings and data?">' +
            '<div id="deactivate-plugin-message">' +
                '<p>Would you like to keep all the settings and data?</p>' +
                '</div>' +
            '</div>';

        $(modalHtml).dialog({
            title: "Keep settings and Data?",
            modal: true,
            buttons: {
                "Delete": function () {
                    let ajaxCall = {
                       'action': 'delete_data'
                    }

                    $('#deactivate-plugin-message').empty().html(
                        '<br><br>' +
                        '<div class="deactivate-loader"><p class="loader-message">Clearing data...</p>' +
                        '<span class="loader-spinner"></span>' +
                        '</div>' +
                        '<br><br><br>'
                    );

                    serviceConnect.connect(ajaxCall).then(function () {
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
