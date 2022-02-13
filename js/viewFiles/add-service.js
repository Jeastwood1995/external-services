jQuery(function ($) {
    let addServiceObj = new EsAddService();

    // Show/remove advanced authorization fields
    $('#auth-header').change(function() {
        addServiceObj.displayOrRemoveAuthorizationSettingsFields($(this));
    });

    // data format dropdown event
    $('#data-format').change(function () {
        addServiceObj.displayOrRemoveCsvSettingsFields($(this));
    });

    // Test connection function that sends form data to Ajax controller, then send curl request to check to see whether service accepts or fails
    $('#test-connection').click(function () {
        addServiceObj.checkConnectionSettings();
    });
});