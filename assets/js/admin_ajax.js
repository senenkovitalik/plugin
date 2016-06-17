jQuery(document).ready( function( $ ) {

    $("#form_db_set").submit(function( event ) {

        event.preventDefault();

        var table = $("#user_table")[0];
        var rows = table.rows;
        var user_id, div, ch, p_arr = [],
            primary_pages, custom_pages, tags, categories, 
            data = [];

        // iterate over rows
        for(var i=0; i<rows.length; i++) {

            // get user id
            user_id = rows[i].getElementsByTagName("td")[0].getElementsByTagName("input")[0].value;
            // get all div's from current row
            div = rows[i].getElementsByTagName("td")[1].childNodes;

            // iterate over divs
            for(var j=0; j<div.length; j++) {
                // get checkboxes
                ch = div[j].getElementsByTagName("input");

                // iterate over checkboxes
                for(var k=0; k<ch.length; k++) {
                    if( ch[k].checked ) {
                        p_arr.push(ch[k].value);
                    }
                }

                switch (j) {
                    case 0:     // primary
                        primary_pages = p_arr.slice();
                        break;
                    case 1:     // custom
                        custom_pages = p_arr.slice();
                        break;
                    case 2:     // tegs
                        tags = p_arr.slice();
                        break;
                    case 3:     // categories
                        categories = p_arr.slice();
                }

                p_arr = [];
            }
            
            // create new object
            obj = {};
            obj.user_id = user_id;
            obj.primary_pages = primary_pages;
            obj.custom_pages = custom_pages;
            obj.tags = tags;
            obj.categories = categories;

            console.log(obj);

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