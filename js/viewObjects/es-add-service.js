EsAddService = function () {
    this._helper = new Helper();

    this.displayOrRemoveAuthorizationSettingsFields = function (checkbox) {
        if (checkbox.is(':checked')) {
            jQuery('#authAddForm').css("display", "block");
        } else {
            jQuery('#authAddForm').css("display", "none");
        }
    }

    this.displayOrRemoveCsvSettingsFields = function (dropdown) {
        // if the selected format is csv, then display parsing options e.g. delimeter, escape character
        if (dropdown.val() === 'csv') {
            jQuery('#csvParseForm').css("display", "block");
        } else {
            jQuery('#csvParseForm').css("display", "none");
        }
    }

    this.submitForm = function (event, form, nonceKey) {
        this._helper.verifyAndSubmitForm(event, form, nonceKey, jQuery('#_wpnonce').val());
    }

    this.showBasicOrTokenAuthFields = function (authSelection) {
        jQuery('.auth-input').css('display', 'none');

        let field = '';

        switch (authSelection.val()) {
            case 'basic':
                field = 'basic-auth';
                break;
            case 'token':
                field = 'token-auth';
                break;
        }

        jQuery('#' + field).css('display', 'block');
    }
}