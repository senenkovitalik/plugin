
<h2>Subscription bar settigs</h2>
<p>You need to add informattion about your accounts. 
If you don't have account yet, please <a href="https://rabbut.com/">visit</a> our web-site.</p>

<?php

    require_once "db.php";

    $d = $this->db->get_data();

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


<form id="form_db_set">
    <table id="user_table">

        <?php 
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
                    $checkbox .= "<input type='checkbox' name='page' value='".$page."'>".$page."<br>";
                }

                echo 
                "<tr id='row_$i'>
                    <td>
                        <input type='text' name='user_id_$i' value='".$user_id."' size='38'>
                    </td>
                    <td id='td_$i'>".$checkbox.
                    "</td>
                </tr>";

                // cleare checkboxes
                $checkbox = "";

                $i++;
            }
        ?>
    </table>

    <input id="add_account" type="button" value="Add User ID" class="button button-primary">
    <input id="submit_btn" type="submit" value="Submit" class="button button-primary">
    <span id="stat" style="vertical-align: middle; margin-left: 20px; color: #00ff00;"></span>
</form>

<script type="text/javascript">

    var table = document.getElementById("user_table"),

        // array of user_id input fields (id)
        user_id_arr = get_user_ids(),

        // array of pages checkboxes (id)
        pages_arr = pages_array(),

        add_account = document.getElementById("add_account"),
        submit_btn = document.getElementById("submit_btn");

    dis_enab_submit();

    // add event "onclick" to Add Acount button
    add_account.addEventListener("click", function() {

        var user_len = user_id_arr.length;   // number of user_id's
        var last = 0;

        if(user_len !== 0) {
            last = user_len;  // index of new field
        }  

        // user can add new account only previous is completely filled
            if(last !== 0) {
                var prev_row = document.getElementById("row_"+(last-1));
        
                if(!check_row(prev_row)) {
                    return;
                }
            }

        var tr = table.insertRow(last);
        tr.id = "row_"+last;

        // first <td> with user_id
            var td_id = tr.insertCell(0);

            // input field for user_id
            var input_id = document.createElement("input");
            input_id.type = "text";
            input_id.name = "user_id_"+last;
            input_id.value = "";
            input_id.size = 38;

            // add <td> to <tr>
            td_id.appendChild(input_id);

        // second <td> with pages
            var td_check = tr.insertCell(1);
            td_check.id = "td_"+last;

            // array of WP_Posts
            var p = get_all_pages();

            for(var i=0; i<p.length; i++) {

                // create new <input> element
                var input_ch = document.createElement("input");
                input_ch.type = "checkbox";
                input_ch.value =  p[i].post_name;

                // create page title
                var text = document.createTextNode(p[i].post_title);
                var br = document.createElement("br");

                td_check.appendChild(input_ch);
                td_check.appendChild(text);
                td_check.appendChild(br);
            }

        tr.addEventListener("mouseenter", function() {
            // show remove button
        });

        tr.addEventListener("mouseleave", function() {
            // hide remove button
        });

        // add info about id's to user_id_arr, pages_arr
        user_id_arr.push(tr.id);

        dis_enab_submit();

        console.log(pages_arr);
    });

    // check to disable or enable submit button
    function dis_enab_submit() {
        // if there no user_id field then Submit button is disabled
        if(get_user_ids().length === 0) {
            submit_btn.disabled = "disabled";
        } else {
            submit_btn.disabled = false;
        }
    }

    // return array of id <tr> user_id 
    function get_user_ids() {
        
        var rows = table.getElementsByTagName("tr");
        var fields = [];
        
        if(rows.length !== 0) {
            for(var i = 0; i<rows.length; i++) {
                fields.push(rows[i].id);
            }
        }

        return fields;
    }

    // return JSON of all WP_post's (pages)
    function get_all_pages() {
        return <?php echo json_encode($pages); ?>;
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

</script>