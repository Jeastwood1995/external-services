EsAddService = function () {
    this._ajaxConnector = new ServiceConnection();

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

    this.checkIfFormIsValid = async function (event, form) {
        if (form.valid()) {
            event.preventDefault();

            let nonceCheckCall = {
                'action': 'check_form_nonce',
                '_wpnonce': jQuery('#_wpnonce').val()
            }

            let ajaxResult = await this._ajaxConnector.connect(nonceCheckCall);

            if (ajaxResult.success === true) {
                jQuery('.loader').css('display', 'block');
                form.submit();
            }
        }
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