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

    this.checkConnectionSettings = async function() {
        // remove the configure service section
        jQuery('#configure-service-section').remove();

        // If all elements on the form are validated, then make the URL connection via AJAX to the AJAX controller
        if (jQuery('#add-service').valid()) {
            try {
                let result = await this._ajaxConnector.connect(jQuery('#add-service').serializeArray(), /*this._loader*/);

                if (result.success === true && result.data != null) {
                    await this._displayConfigureDataBox(result);
                }
            } catch (e) {
                console.log("There's been an error: " + e);
            }
        }
    }

    this._displayConfigureDataBox = async function(testConnectionResult) {
        let viewCall = {
            'action': 'call_view',
            'view': 'add-service/configureService',
            'class': 'ExternalServices\\Classes\\Blocks\\Configure_Service',
            'data': jQuery.parseJSON(testConnectionResult.data)
        };

        try {
            let result = await this._ajaxConnector.connect(viewCall);

            if (result.success === true) {
                jQuery('#add-service-box').after(result.data);
            }
        } catch (e) {
            console.log("There's been an error: " + e);
        }
    }
}