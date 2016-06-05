
jQuery(document).ready( function( $ ) {

    $("#header_form").submit(function( event ) {
        event.preventDefault();
        var username = $("#username").val();
        var email = $("#email").val();

        if ( username !== "" && email !== "" ) {
            // data for post request
            var data = {
                action: 'action',
                username: username,
                email: email
            };

            // send data to server
            $.post( svi_object.ajax_url, data,

                // get response
                function( response ) {
                    alert( response );
                }
            );
        }
    });
});