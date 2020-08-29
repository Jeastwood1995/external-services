jQuery(function ($) {

    // First of all remove that annoying first level item from the main menu
    $('.toplevel_page_external-services-menu .wp-first-item').remove();

    // Show authorization field if keycheck is checked
    $('#keyCheck').change(function() {
        if ($('#keyCheck').is(':checked')) {
            let keyField = $('<li id="authKey"><label id="authLabel" for="authKey">Authorization Key: </label>' +
                '<input type="input" name="authKey"></li>');
            $(keyField).insertAfter('#keyCheckField');
        } else {
            if ($('#authKey').length) {
                $('#authKey').remove();
            }
        }
    });

    // Test connection function that sends form data to Ajax controller, then send curl request to check to see whether service accepts or fails
    $('#test-connection').click(function (){
        let form = $('#add-service').serializeArray();

        $.ajax({
            type: "POST",
            url: external_services.ajax_post,
            data: form ,
            success: function (response) {
                console.log(response);
            },
            error: function (xhr, status, error) {
                //console.log(xhr.responseText);
                alert('Error during connection to the service. Error Message: ' + xhr.responseText);
            }
        });
    });

    /*
    // Get add form data
    $('#add-service').submit(function (e) {
        e.preventDefault();

        let form = $(this);
        let url = form.attr('action');

        $.ajax({
            type: "POST",
            url: url,
            data: {
             form: form.serialize(),
            },
            success: function (response) {
                $(response.data).insertAfter('#wptitle');
            },
            error : function(error){
                console.log(error)
            }
        });

    });
     */
});