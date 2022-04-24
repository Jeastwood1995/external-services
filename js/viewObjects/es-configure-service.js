EsConfigureService = function () {
    this.massSelectFields = function (selectAll) {
        let mappingCheckboxes = jQuery('#configure-service').find('.data-mappings');

        selectAll.is(':checked') ? jQuery(mappingCheckboxes).prop('checked', true) : jQuery(mappingCheckboxes).prop('checked', false);
    }

    this.showOrHideDataSettingsFields = function(checkbox) {
        let checkboxId = checkbox.id;
        let settingsBox = jQuery("tr.data-map-settings." + checkboxId);

        if (jQuery(checkbox).is(':checked')) {
            settingsBox.css("display", "block");
        } else {
            settingsBox.css("display", "none");
        }
    }
}