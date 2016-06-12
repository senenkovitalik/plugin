jQuery(document).ready( function( $ ) {

    $("#form_db_set").submit(function( event ) {

        event.preventDefault();

        var table = $("#user_table")[0];
        var rows = table.rows;
        var user_id, ch, pages=[], obj, data = [];

        // iterate over rows
        for(var i=0; i<rows.length; i++) {

            // get user id
            user_id = rows[i].getElementsByTagName("td")[0].getElementsByTagName("input")[0].value;
            // get all checkboxes from current row
            ch = rows[i].getElementsByTagName("td")[1].getElementsByTagName("input");

            // iterate over checkboxes
            for(var j=0; j<ch.length; j++) {
                if(ch[j].checked === true) {
                    pages.push(ch[j].value);
                }
            }

            // create new object
            obj = {};
            obj.user_id = user_id;
            obj.pages = pages;
            
            // clear array
            pages = [];

            // add object to array
            data.push(obj);     // data for post request
        }

        // send data to server
        $.post(
            admin_object.ajax_url,
            {'action': 'admin_action', 'data': data},
            // get response
            function( response ) {
                $("#stat").text( response );
            }
        );

    });
});