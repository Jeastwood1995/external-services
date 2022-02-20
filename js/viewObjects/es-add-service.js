 EsAddService = function() {
     this._ajaxConnector = new ServiceConnection();

    this.displayOrRemoveAuthorizationSettingsFields = async function(checkbox) {
        if (checkbox.is(':checked')) {
            let viewCall = {
                'action': 'call_view',
                'view': 'add-service/authHeaderConfigure'
            };

            let result = await this._ajaxConnector.connect(viewCall);

            if (result.success === true) {
                jQuery('#authHeadField').after(result.data);
            }
        } else {
            jQuery('#authAddForm').length ? jQuery('#authAddForm').remove() : '';
        }
    }

    this.displayOrRemoveCsvSettingsFields = async function(dropdown) {
        // if the selected format is csv, then display parsing options e.g. delimeter, escape character
        if (dropdown.val() === 'csv') {
            let viewCall = {
                'action': 'call_view',
                'view': 'add-service/csvParseSettings'
            };

            let result = await this._ajaxConnector.connect(viewCall);

            if (result.success === true) {
                jQuery('#dataFormatField').after(result.data);
            }
        } else {
            jQuery('#csvParseForm').length ? jQuery('#csvParseForm').remove() : '';
        }
    }
    
    this.checkIfFormIsValid = async function(event, form) {
        if (form.valid()) {         
            event.preventDefault();       
            

            let nonceCheckCall = {
                'action': 'check_form_nonce',
                '_wpnonce': jQuery('#_wpnonce').val()
            }
            
            let ajaxResult = await this._ajaxConnector.connect(nonceCheckCall);
            
            if (ajaxResult.success === true) {
                form.submit();
            }
        }
    }
}