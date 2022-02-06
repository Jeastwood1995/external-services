ServiceConnection = function () {
    this._wpAjaxUrl = serviceConnect.wp_ajax_url;

    /**
     * Connect to a passed in URL
     *
     * @param form
     * @param loader
     */
    this.connect = function (form, loader = '') {
        return jQuery.ajax({
            type: "POST",
            url: this._wpAjaxUrl,
            data: form,
            beforeSend: function () {
                (loader !== '') ? loader.css('display', 'flex') : '';
            },
            error: function (xhr) {
                let message = jQuery.parseJSON(xhr.responseText);
                alert('Error during connection to the URL. Error Message: \n\n' + message.data);
            },
            fail: function (xhr) {
                let message = jQuery.parseJSON(xhr.responseText);
                alert('Connection to the service failed. Message: \n\n' + message.data);
            },
            complete: function () {
                (loader !== '') ? loader.css('display', 'none') : '';
            }
        });
    }
}