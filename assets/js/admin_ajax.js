/**
 * Created by user on 23.01.16.
 */

jQuery(document).ready( function( $ ) {

    $("#form_db_set").submit(function( event ) {

        event.preventDefault();

        var user_id = $("#user_id").val();

            // data for post request
            var data = {
                action: 'admin_action',
                user_id: user_id
            };

            // send data to server
            $.post( admin_object.ajax_url, data,

                // get response
                function( response ) {
                    $("#stat").text( response );
                }
            );

    });
});