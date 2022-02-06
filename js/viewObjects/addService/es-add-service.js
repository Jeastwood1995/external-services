jQuery(function () {
    let object = new EsAddService();
    debugger;
});

class EsAddService {
    constructor() {
        debugger;
        this._ajaxConnector = new ServiceConnection();
    }
}

/*
AddService = function () {
    debugger;
    this._ajaxConnector = new ServiceConnection();

    this.displayOrRemoveAuthorizationField = async function (hello) {
        if ($(this).is(':checked')) {
            let viewCall = {
                'action': 'call_view',
                'view': 'add-service/authHeaderConfigure'
            };

            let result = await this._ajaxConnector.connect(viewCall);

            if (result.success === true) {
                $('#authHeadField').after(result.data);
            }
        } else {
            $('#authAddForm').length ? $('#authAddForm').remove() : '';
        }
    }
}
*/