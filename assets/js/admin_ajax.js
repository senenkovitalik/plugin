jQuery(document).ready( function( $ ) {

    $("#form_db_set").submit( function( event ) {

        event.preventDefault();

        var table = $("#user_table")[0];
        
        var rows = table.rows;
        var userID, div, ch, p_arr = [],
            primaryPages, customPages, tags, categories, 
            data = [];

        for (var i = 0; i < rows.length; i++) {
            userID = rows[i].getElementsByTagName("td")[0].getElementsByTagName("input")[0].value;
            div = rows[i].getElementsByTagName("td")[1].getElementsByTagName("div");

            for (var j = 0; j < div.length; j++) {
                // get checkboxes
                ch = div[j].getElementsByTagName("input");

                for (var k = 0; k < ch.length; k++) {
                    if (ch[k].checked) {
                        p_arr.push(ch[k].value);
                    }
                }

                switch (j) {
                    case 0:     // primary
                        primaryPages = p_arr.slice();
                        break;
                    case 1:     // custom
                        customPages = p_arr.slice();
                        break;
                    case 2:     // tegs
                        tags = p_arr.slice();
                        break;
                    case 3:     // categories
                        categories = p_arr.slice();
                }

                p_arr = [];
            }
            
            obj = {};
            obj.user_id = userID;
            obj.primary_pages = primaryPages;
            obj.custom_pages = customPages;
            obj.tags = tags;
            obj.categories = categories;

            data.push(obj);     // data for post request
        }

        console.log(data);

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