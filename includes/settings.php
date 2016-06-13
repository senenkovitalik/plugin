
<h2>Subscription bar settigs</h2>
<p>You need to add informattion about your accounts. 
If you don't have account yet, please <a href="https://rabbut.com/">visit</a> our web-site.</p>

<form id="form_db_set">
    <table id="user_table">

        <?php 

            require_once "db.php";

            $d = $this->db->get_data();
            $i = 0;

            foreach ($d as $row) {

                // get user id
                $user_id = $row['user_id'];
                // remove all whitespaces
                $pages = preg_replace('/\s+/', '', $row['pages']);
                // brake string into array
                $pages_arr = explode(',', $pages);

                // create checkboxes
                $checkbox;
                foreach ($pages_arr as $page) {
                    if($page !== "") {
                        $checkbox .= "<input type='checkbox' name='page' value='".$page."' checked required>".$page."<br>";
                    }
                }

                echo 
                "<tr style='height: 33px;'>
                    <td>
                        <input type='text' name='user_id_$i' value='".$user_id."' size='38' required>
                    </td>
                    <td>".$checkbox.
                    "</td>
                </tr>";

                // clear checkboxes
                $checkbox = "";

                $i++;
            }
        ?>
    </table>

    <div style="position: relative; left: 5px;">
        <input id="add_account" type="button" value="Add User ID" class="button button-primary" ">
        <input id="submit_btn" type="submit" value="Submit" class="button button-primary">
        <span id="stat" style="vertical-align: middle; margin-left: 20px;"></span>
    </div>
    
</form>

<script type="text/javascript">

    var table = document.getElementById("user_table"),
        add_account = document.getElementById("add_account"),
        submit_btn = document.getElementById("submit_btn");

    add_listeners();
    dis_enab_submit();

    // add event "onclick" to Add Acount button
    add_account.addEventListener("click", function() {

        var row_count = table.rows.length;   // number of rows
        var last = 0;
        var prev_row;

        if(row_count !== 0) {
            last = row_count;  // index of new field
        }  

        // user can add new account if there are unused pages
            var unused = unused_pages();
            if(unused.length === 0) {
                return;
            }

        // user can add new account only previous is completely filled
        // prev_row it's same as last row, cause "current row" is not added yet
            if(last !== 0) {
                prev_row = table.rows[row_count-1];
        
                if(!check_row(prev_row)) {
                    return;
                }
            }

        // clear status message
        document.getElementById("stat").innerHTML = "";

        // when user add new account - remove unused checkboxes
        remove_unused_check(prev_row);

        // insert row 
            var tr = table.insertRow(last);
            tr.style.height = "33px";

        // first <td> with user_id
            var td_id = tr.insertCell(0);

            // input field for user_id
            var input_id = document.createElement("input");
            input_id.type = "text";
            input_id.name = "user_id_"+last;
            input_id.value = "";
            input_id.size = 38;
            input_id.required = true;

            // add <td> to <tr>
            td_id.appendChild(input_id);

        // second <td> with pages
            var td_check = tr.insertCell(1);

            for(var i=0; i<unused.length; i++) {

                // create new <input> element
                var input_ch = document.createElement("input");
                input_ch.type = "checkbox";
                input_ch.value =  unused[i];
                input_ch.required = true;

                // create page title
                var text = document.createTextNode(unused[i]);
                var br = document.createElement("br");

                td_check.appendChild(input_ch);
                td_check.appendChild(text);
                td_check.appendChild(br);
            }

        // third <td> with remove button
            var td_remove = tr.insertCell(2);

            // create remove button
            var button = document.createElement("input");
            button.type = "button";
            button.value = "Remove";
            button.style.visibility = "hidden";

            // remove <tr> when user click on button
            button.onclick = (function() {
                tr.parentNode.removeChild(tr);
                dis_enab_submit();
                document.getElementById("stat").innerHTML = "";
            });

            td_remove.appendChild(button);

        tr.addEventListener("mouseenter", function() {
            // show remove button
            button.style.visibility = "visible";  
        });

        tr.addEventListener("mouseleave", function() {
            // hide remove button
            button.style.visibility = "hidden";
        });

        dis_enab_submit();
    });

    // check to disable or enable submit button
    function dis_enab_submit() {
        // if there no user_id field then Submit button is disabled
        if(table.rows.length === 0) {
            submit_btn.disabled = true;
        } else {
            submit_btn.disabled = false;
        }
    }

    // return JSON of all WP_post's (pages)
    function get_all_pages() {
        <?php 
            $args = array(
                        'sort_order'   => 'ASC',
                        'sort_column'  => 'post_title',
                        'hierarchical' => 1,
                        'exclude'      => '',
                        'include'      => '',
                        'meta_key'     => '',
                        'meta_value'   => '',
                        'authors'      => '',
                        'child_of'     => 0,
                        'parent'       => -1,
                        'exclude_tree' => '',
                        'number'       => '',
                        'offset'       => 0,
                        'post_type'    => 'page',
                        'post_status'  => 'publish',
                    ); 
            // get array of WP_post's
            $pages = get_pages( $args );
        ?>
        return <?php echo json_encode($pages); ?>;
    }

    // return array of used pages
    function get_used_pages() {
        var row, td, input, id_arr = table.rows, fields = [];
        if(id_arr.length === 0) {
            return fields;
        }
        for(var i=0; i<id_arr.length; i++) {
            row = id_arr[i];
            td = row.getElementsByTagName("td")[1];
            input = td.getElementsByTagName("input");
            for(var j=0; j<input.length; j++) {
                if(input[j].checked === true) {
                    fields.push(input[j].value);
                }
            }
        }
        return fields;
    }

    // check that all field of row are filled
    // return true|false
    function check_row(row) {

        var td_id = row.getElementsByTagName("td")[0].getElementsByTagName("input")[0];
        var td = row.getElementsByTagName("td")[1];
        // array of input fields (checkboxes)
        var chbox = td.getElementsByTagName("input");

        var ch = false;
        // check that all checkboxes are checked or not
        for(var i=0; i<chbox.length; i++) {
            if(chbox[i].checked) {
                ch = true;
                break;
            } else {
                ch = false;
            }
        }

        // if row empty or checkboxes not checked
        // true - all fields are filled
        // false - some fields are not filled
        if(td_id.value !== "" && ch) {
            return true;
        } else {
            return false;
        }
    }

    // return array of unused pages
    function unused_pages() {
        var wp_pages = get_all_pages();
        var used_pages = get_used_pages();
        var unused = [];
        var match;

        if(used_pages.length === 0) {
            for(var i=0; i<wp_pages.length; i++) {
                unused.push(wp_pages[i].post_name);
            }
            return unused;
        }

        for(var i=0; i<wp_pages.length; i++) {
            match = false;
            for(var j=0; j<used_pages.length; j++) {
                if(wp_pages[i].post_name === used_pages[j]) {
                    match = true;
                    break;
                } else {
                    match = false;
                }
            }
            if(!match) unused.push(wp_pages[i].post_name);
        }
        return unused;
    }

    // add event listeners to table rows generated by PHP
    function add_listeners() {

        for(var i=0; i<table.rows.length; i++) {

            // create third <td>
            var td_remove = table.rows[i].insertCell(2);

            // create remove button
            var button = document.createElement("input");
            button.type = "button";
            button.value = "Remove";
            button.style.visibility = "hidden";

            button.onclick = (function() {
                var tr = this.parentNode.parentNode;
                table.tBodies[0].removeChild(tr);
                dis_enab_submit();
                document.getElementById("stat").innerHTML = "";
            });

            td_remove.appendChild(button);

            table.rows[i].addEventListener("mouseenter", function() {
                // show remove button
                this.cells[2].getElementsByTagName("input")[0].style.visibility = "visible";
            });

            table.rows[i].addEventListener("mouseleave", function() {
                // hide remove button
                this.cells[2].getElementsByTagName("input")[0].style.visibility = "hidden";
            });
        }
    }

    // remove unused checkboxes
    function remove_unused_check(row) {
        // if we have row
        if(row !== undefined) {
            // <td> with inputs
            var td = row.cells[1];
            // get array of input fields
            var input_arr = td.getElementsByTagName("input");
            // input tag (checkbox)
            var ch;
            for(var i=input_arr.length; i>0; i--) {
                ch = input_arr[i-1];
                if( !ch.checked ) {
                    td.removeChild(ch.nextElementSibling); // <br>
                    td.removeChild(ch.nextSibling);        // text
                    td.removeChild(ch);                    // <input>
                }
            }
        }   
    }
</script>