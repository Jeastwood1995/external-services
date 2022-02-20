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

    // check whether form is valid
    $('#add-service').one('submit', function(event) {
        addServiceObj.checkIfFormIsValid(event, $(this));
    });
});