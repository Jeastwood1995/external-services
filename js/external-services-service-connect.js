ServiceConnection = function (url) {
    this.baseUrl = url;

    /**
     * Connect to a passed in URL
     *
     * @param form
     * @param loader
     */
    this.connect = function(form, loader = '') {
        jQuery.ajax({
            type: "POST",
            url: this.baseUrl,
            data: form ,
            beforeSend: function() {
                (loader !== '') ? loader.css('display', 'flex') : '';
            },
            success: function (response) {
                return response;
            },
            error: function (xhr, status, error) {
                let message = $.parseJSON(xhr.responseText);
                alert('Error during connection to the URL. Error Message: \n\n' + message.data);
            },
            fail: function(xhr, textStatus, errorThrown) {
                debugger;
            },
            complete: function() {
                (loader !== '') ? loader.css('display', 'none') : '';
            }
        });
    }
}