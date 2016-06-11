
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
        tbody = table.getElementsByTagName("tbody")[0];

        // array of user_id input fields (id)
        user_id_arr = (function() {
            // rows array
            var rows = table.getElementsByTagName("tr");
            var fields = [];
            
            if(rows.length !== 0) {
                for(var i = 0; i<rows.length; i++) {
                    fields.push(rows[i].id);
                }
            }

            return fields;
        })(),

        // array of pages checkboxes (id)
        pages_arr = (function() {
            // array of <tr>
            var rows = table.getElementsByTagName("tr");
            // array to return
            var fields = [];

            for(var i = 0; i<rows.length; i++) {
                // array of <td>
                var td = rows[i].getElementsByTagName("td");
                fields.push(td[1].id);
            }

            return fields;
        })(),

        add_account = document.getElementById("add_account"),
        submit_btn = document.getElementById("submit_btn");

        dis_enab_submit();

        // add event "onclick" to Add Acount button
        add_account.addEventListener("click", function() {

            // event.preventDefault();

            var user_len = user_id_arr.length;   // number of user_id's
            var last = 0;

            if(user_len !== 0) {
                last = user_len+1;  // index of new field
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

            var input_ch = document.createElement("input");
            input_ch.type = "checkbox";
            input_ch.name = "page";
            input_ch.value = "some page";       // !!!!!!!!!!!!!!!!!

            // text to checkbox
            var text = document.createTextNode("some page");

            td_check.appendChild(input_ch);
            td_check.appendChild(text);

            dis_enab_submit();
        });

        function dis_enab_submit() {
            // if there no user_id field then Submit button is disabled
            if(user_id_arr.length === 0) {
                submit_btn.disabled = "disabled";
            } else {
                submit_btn.disabled = false;
            }
        }
</script>