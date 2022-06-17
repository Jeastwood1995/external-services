Helper = function () {
    this._ajaxConnector = new ServiceConnection();

    this.checkIfFormIsValid = async function (event, form, nonceKey, nonceVal) {
        if (form.valid()) {
            event.preventDefault();

            this._displayCheckingNonceLoader();

            let nonceCheckCall = {
                'action': 'check_form_nonce',
                'nonce_key': nonceKey,
                'nonce_val': nonceVal,
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
        jQuery('.postbox .loader-message').text('Verifying form submission...');
        jQuery('.postbox .loader-spinner').css('width', '105px').css('height', '105px');
        jQuery('#add-service-box .loader').show();
    }

    this._displayProcessingDataLoader = function () {
        jQuery('.postbox .loader-message').text('Processing data...');
        jQuery('.postbox .loader-spinner').css('width', '90px').css('height', '90px');
    }
}