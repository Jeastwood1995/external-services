EsConfigureService = function () {
    this.massSelectFields = function (selectAll) {
        let mappingCheckboxes = jQuery('#configure-service').find('.data-mappings');
        let settingsBox = jQuery("tr.data-map-settings");

        if (selectAll.is(':checked')) {
            jQuery(mappingCheckboxes).prop('checked', true);
            settingsBox.each(function (index, settingBox) {
                jQuery(settingBox).css("display", "block");
            });
        } else {
            jQuery(mappingCheckboxes).prop('checked', false);
            settingsBox.each(function (index, settingBox) {
                jQuery(settingBox).css("display", "none");
            });
        }
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