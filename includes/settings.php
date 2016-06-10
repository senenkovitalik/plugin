
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
                    <td>".$checkbox.
                    "</td>
                </tr>";

                // cleare checkboxes
                $checkbox = "";

                $i++;
            }
        ?>

        <tr id="buttons">
            <td colspan="2">
                <input id="add_field" type="button" value="Add User ID" class="button button-primary" onclick="addField();">
                <input id="submit_btn" type="submit" value="Submit" class="button button-primary">
                <span id="stat" style="vertical-align: middle; margin-left: 20px; color: #00ff00;"></span>
            </td>
        </tr>
    </table>
</form>

<script type="text/javascript">
    var table = document.getElementById("user_table"),
        user_id_arr = (function() {
            var rows = table.getElementsByTagName("tbody")[0].getElementsByTagName("tr");
            var fields = [];
            for(var i = 0; i<rows.length; i++) {
                if(rows[i].id !== "buttons") {
                    fields.push(rows[i].id);
                }
            }

            return fields;
        })(),
        pages_arr = (function() {
            
        })(),
        add_field_btn = document.getElementById("add_field"),
        submit_btn = document.getElementById("submit_btn");

        console.dir(user_id_arr);
</script>