
<h2>Subscription bar settigs</h2>
<p>You need to add informattion about your accounts. 
If you don't have account yet, please <a href="https://rabbut.com/">visit</a> our web-site.</p>

<form id="form_db_set">
    <table id="user_table">

        <?php 

            require_once "db.php";

            // get all data from DB
            $d = $this->db->get_data();

            // array of page's types
            $type = array('primary_pages', 'custom_pages', 'tags', 'categories');

            // iterate over table rows
            foreach ($d as $row) {

                // data from DB ( saved by user )
                $user_id = $row['user_id'];

                // iterate over pages
                for( $k=1; $k<count($row); $k++ ) {
                    
                    // brake string into array
                    $pages_arr = explode(', ', $row[$type[$k-1]]);

                    switch ( $k ) {

                        case 1:
                            // primary_pages
                            $p = array('Main Page', 'Front Page', 'Blog Page', 'Posts');
                            // iterate over primary pages 
                            foreach($p as $pi) {
                                // check that $pages_arr contain page $pi
                                if( in_array($pi, $pages_arr) ) {
                                    // if so, checkbox is checked
                                    $pp .= "<input type='checkbox' value='".$pi."' checked>".$pi."<br>";
                                } else {
                                    // not checked
                                    $pp .= "<input type='checkbox' value='".$pi."'>".$pi."<br>";
                                }
                            }
                            
                            break;

                        case 2:
                            // custom_pages
                            $p = get_pages();
                            // iterate over custom pages
                            foreach ($p as $pi ) {
                                // check that $pages_arr contain page $pi
                                if( in_array($pi->post_title, $pages_arr) ) {
                                    // if so, checkbox is checked
                                    $cp .= "<input type='checkbox' value='".$pi->post_title."' checked>".$pi->post_title."<br>";
                                } else {
                                    // not checked
                                    $cp .= "<input type='checkbox' value='".$pi->post_title."'>".$pi->post_title."<br>";
                                }
                            }

                            break;

                        case 3:
                            // tags, get all tags, if tag empty it not return, so we need 'hide_empty' => false
                            $tags = get_tags( array('hide_empty' => false) ); // data from WPDB 
                            // iterate over tags
                            foreach ($tags as $tag) {
                                // check that $pages_arr contain tag $tag
                                if( in_array($tag->name, $pages_arr) ) {
                                    // if so, checkbox is checked
                                    $t .= "<input type='checkbox' value='".$tag->name."' checked>".$tag->name."<br>";
                                } else {
                                    // not checked
                                    $t .= "<input type='checkbox' value='".$tag->name."'>".$tag->name."<br>";
                                }
                            }

                            break;

                        case 4:
                            // categories
                            $categories = get_categories();
                            // iterate over categories
                            foreach ($categories as $cat) {
                                // check that $pages_arr contain category $cat
                                if( in_array($cat->name, $pages_arr) ) {
                                    // if so, checkbox is checked
                                    $c .= "<input type='checkbox' value='".$cat->name."' checked>".$cat->name."<br>";
                                } else {
                                    // not checked
                                    $c .= "<input type='checkbox' value='".$cat->name."'>".$cat->name."<br>";
                                }
                            }

                            break; 
                    }
                }

                // output table row
                echo 
                "<tr style='height: 33px;'>
                    <td>
                        <input type='text' value='".$user_id."' size='38' required>
                    </td>
                    <td>
                        <div id='Primary Pages'>Primary Pages<br>".$pp."</div>
                        <div id='Custom Pages'>Custom Pages<br>".$cp."</div>
                        <div id='Tags'>Tags<br>".$t."</div>
                        <div id='Categories'>Categories<br>".$c."</div>
                    </td>
                    <td>
                    </td>
                </tr>";

                // clear variables for next use
                $pp = ""; $cp=""; $t=""; $c="";
            }
        ?>
    </table>

    <div style="position: relative; left: 5px;">
        <input id="add_account" type="button" value="Add User ID" class="button button-primary" ">
        <input id="submit_btn" type="button" value="Submit" class="button button-primary">
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

        // insert row 
            var tr = table.insertRow(last);
            tr.style.height = "33px";

        // first <td> with user_id
            var td_id = tr.insertCell(0);

            // input field for user_id
            var input_id = document.createElement("input");
            input_id.type = "text";
            input_id.value = "";
            input_id.size = 38;
            input_id.required = true;

            // add <td> to <tr>
            td_id.appendChild(input_id);

        // second <td> with pages
            var td_check = tr.insertCell(1);

                var all = all_data();
                var primary = all[0].primary;
                var custom = all[1].custom;
                var tags = all[2].tags;
                var categories = all[3].categories;

                var div, text, br, input_ch, j=0;

                var t = ["Primary pages", "Custom pages", "Tags", "Categories"];
                for(var o in t) {
                    div = document.createElement("div");
                    div.id = t[o];
                    text = document.createTextNode(t[o]);
                    br = document.createElement("br");
                    div.appendChild(text);
                    div.appendChild(br);

                    var data = [primary, custom, tags, categories];

                    for (var i=0; i<data[j].length; i++) {
                        // create new <input> element
                        input_ch = document.createElement("input");
                        input_ch.type = "checkbox";
                        input_ch.value =  data[j][i];

                        // create page title
                        text = document.createTextNode(data[j][i]);
                        br = document.createElement("br");

                        div.appendChild(input_ch);
                        div.appendChild(text);
                        div.appendChild(br);
                    }
                    j++;
                    td_check.appendChild(div);
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

    // check that all field of row are filled
    // return true|false
    function check_row( row ) {

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

    // create object of lists of pages, tags, categories
    function all_data() {
        <?php 
            $tags = get_tags( array('hide_empty'   => false) );
            $tags_arr = array(); 
            foreach ($tags as $tag) {
                array_push($tags_arr, $tag->name);
            }

            $categories = get_categories();
            $cat_arr = array();
            if( $categories ) {
                foreach ($categories as $cat) {
                    array_push($cat_arr, $cat->name);
                }
            }

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
            $page_arr = array();
            $pages = get_pages( $args );
            if( $pages ) {
                foreach ($pages as $page) {
                    array_push($page_arr, $page->post_title);
                }
            }

            $json = array(
                array('primary' => array('Main Page', 'Front Page', 'Blog Page', 'Posts')),
                array('custom' => $page_arr),
                array('tags' => $tags_arr),
                array('categories' => $cat_arr)
            );
        ?>

        return <?php echo json_encode($json); ?>;
    }

</script>