/**
 * Created by user on 23.01.16.
 */

jQuery(document).ready( function( $ ) {

    $("#form_db_set").submit(function( event ) {

        event.preventDefault();

        var dbtype = $("#dbtype").val();
        var server = $("#server").val();
        var dbname = $("#dbname").val();
        var dbtable = $("#dbtable").val();
        var dbuser = $("#dbuser").val();
        var dbpass = $("#dbpass").val();

            // data for post request
            var data = {
                action: 'admin_action',
                dbtype: dbtype,
                server: server,
                dbname: dbname,
                dbtable: dbtable,
                dbuser: dbuser,
                dbpass: dbpass
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