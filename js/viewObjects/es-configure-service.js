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

    this.onFieldMapChangeHandle = function(select) {
        let parent = select.closest('.data-map-settings');

        switch (select.val()) {
            case "category":
                parent.find('.product-category').css("display", "block");
                parent.find('.product-tags').css("display", "none");
                break;
            case "tag":
                parent.find('.product-tags').css("display", "block");
                parent.find('.product-category').css("display", "none");
                break;
            default:
                parent.find('.product-category').css("display", "none");
                parent.find('.product-tags').css("display", "none");
                break;
        }
    }
}