EsConfigureService = function () {
    this._helper = new Helper();

    this.massSelectFields = function (selectAll) {
        let mappingCheckboxes = jQuery('#configure-service').find('.data-mappings');
        let settingsBox = jQuery("tr.data-map-settings");

        if (selectAll.is(':checked')) {
            jQuery(mappingCheckboxes).prop('checked', true);
            settingsBox.each(function (index, settingBox) {
                let fieldMapInput = settingsBox.find('.field-map-select');
                jQuery(settingBox).css("display", "block");
                fieldMapInput.prop('required', true);
            });
        } else {
            jQuery(mappingCheckboxes).prop('checked', false);
            settingsBox.each(function (index, settingBox) {
                let fieldMapInput = settingsBox.find('.field-map-select');
                jQuery(settingBox).css("display", "none");
                fieldMapInput.prop('required', false);
            });
        }
    }

    this.showOrHideDataSettingsFields = function(checkbox) {
        let checkboxId = checkbox.id;
        let settingsBox = jQuery("tr.data-map-settings." + checkboxId);
        let fieldMapInput = settingsBox.find('.field-map-select');

        if (jQuery(checkbox).is(':checked')) {
            settingsBox.css("display", "block");
            fieldMapInput.prop('required', true);
        } else {
            settingsBox.css("display", "none");
            fieldMapInput.prop('required', false);
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

    this.checkIfFormIsValid = function (event, form, nonceKey) {
        this._helper.checkIfFormIsValid(event, form, nonceKey, jQuery('#_wpnonce').val());
    }
}