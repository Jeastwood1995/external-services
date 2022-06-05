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

    this.checkIfFormIsValid = async function (event, form, nonceKey) {
        if (form.valid()) {
            event.preventDefault();

            this._displayCheckingNonceLoader();

            let nonceCheckCall = {
                'action': 'check_form_nonce',
                'nonce_key': nonceKey,
                'nonce_val': jQuery('#_wpnonce').val(),
            }

            let ajaxResult = await this._ajaxConnector.connect(nonceCheckCall);

            if (ajaxResult.success === true) {
                this._displayProcessingDataLoader();
                form.submit();
            } else {
                jQuery('#add-service-box .loader').hide();
            }
        }
    }

    this._displayCheckingNonceLoader = function () {
        jQuery('#add-service-box .loader-message').text('Verifying form submission...');
        jQuery('#add-service-box .loader-spinner').css('width', '105px').css('height', '105px');
        jQuery('#add-service-box .loader').show();
    }

    this._displayProcessingDataLoader = function () {
        jQuery('#add-service-box .loader-message').text('Processing data...');
        jQuery('#add-service-box .loader-spinner').css('width', '90px').css('height', '90px');
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