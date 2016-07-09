<div class="wrap">

<h1>Subscription bar settigs</h1>
<p>You need to add informattion about your accounts. 
If you don't have account yet, please <a href="https://rabbut.com/">visit</a> our web-site.</p>

<form id="form_db_set">
    <table id="user_table">
        <?php 
            require_once "db.php";

            // get all data from DB
            $d = $this->db->get_data();
            $type = array('primary_pages', 'custom_pages', 'tags', 'categories');

            // iterate over table rows
            foreach ( $d as $row ) {
                // data from DB ( saved by user )
                $user_id = $row['user_id'];

                // iterate over pages
                for( $k = 1; $k < count( $row ); $k++ ) {
                    
                    // brake string into array
                    $pages_arr = explode(', ', $row[$type[$k-1]]);

                    switch ( $k ) {
                        case 1:
                            // primary_pages
                            $p = array('Main Page', 'Front Page', 'Blog Page', 'Posts');
                            $pp = "";

                            foreach ( $p as $pi ) {

                                if ( in_array($pi, $pages_arr) ) {
                                    $pp .= "<input type='checkbox' value='".$pi."' checked>".$pi."<br>";
                                } else {
                                    $pp .= "<input type='checkbox' value='".$pi."'>".$pi."<br>";
                                }
                            }
                            break;

                        case 2:
                            // custom_pages
                            $p = get_pages();
                            $cp = "";

                            foreach ( $p as $pi ) {

                                if( in_array($pi->post_title, $pages_arr) ) {
                                    $cp .= "<input type='checkbox' value='".$pi->post_title."' checked>".$pi->post_title."<br>";
                                } else {
                                    $cp .= "<input type='checkbox' value='".$pi->post_title."'>".$pi->post_title."<br>";
                                }
                            }
                            break;

                        case 3:
                            // tags, 
                            // get all tags, if tag empty - it not return, so we need 'hide_empty' => false
                            $tags = get_tags( array('hide_empty' => false) ); // data from WPDB 
                            $t = "";

                            foreach ( $tags as $tag ) {
                                
                                if( in_array($tag->name, $pages_arr) ) {
                                    $t .= "<input type='checkbox' value='".$tag->name."' checked>".$tag->name."<br>";
                                } else {
                                    $t .= "<input type='checkbox' value='".$tag->name."'>".$tag->name."<br>";
                                }
                            }
                            break;

                        case 4:
                            // categories
                            $categories = get_categories();
                            $c;

                            foreach ( $categories as $cat ) {

                                if ( in_array($cat->name, $pages_arr) ) {
                                    $c .= "<input type='checkbox' value='".$cat->name."' checked>".$cat->name."<br>";
                                } else {
                                    $c .= "<input type='checkbox' value='".$cat->name."'>".$cat->name."<br>";
                                }
                            }
                            break; 
                    }
                }

                // output table row
                echo 
                "<tr>
                    <td>
                        <input type='text' value='".$user_id."' size='38' 
                        pattern='[0-9A-z]{8}-[0-9A-z]{4}-[0-9A-z]{4}-[0-9A-z]{4}-[0-9A-z]{12}'
                        title='Type your ID in format XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX' required>
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
                unset($pp, $cp, $t, $c);
            }
        ?>
    </table>
    <div id="form_buttons">
        <input id="newAccountBtn" type="button" value="Add User ID" class="button button-primary" ">
        <input id="submitBtn" type="submit" value="Submit" class="button button-primary">
        <span id="stat"></span>
    </div> 
</form>
<script type="text/javascript">

    var table = document.getElementById("user_table"),
        newAccountBtn = document.getElementById("newAccountBtn"),
        submitBtn = document.getElementById("submitBtn"),
        checkedArr;

    addListeners();
    // disEnabSubmit();
    disEnabChbox();
    
    // add event "onclick" to Add Acount button
    newAccountBtn.addEventListener("click", function() {

        var rowCount = table.rows.length, last = 0, prevRow, tr, tdID,
            inputID, tdCheck, all, primary, custom, tags, categories,
            div, text, br, inputCh, j, t,data, tdRemove, button;

        // if table contain rows
        if(rowCount !== 0) {
            // index of new field
            last = rowCount; 
        }  

        // user can add new account only previous is completely filled
        // prevRow it's same as last row, cause "current row" is not added yet
        if(last !== 0) {
            prevRow = table.rows[rowCount-1];
    
            // if prev row not filled
            if( ! checkRow(prevRow)) {
                return;
            }
        }

        // clear status message
        document.getElementById("stat").innerHTML = "";

        // insert row 
        tr = table.insertRow(last);
        tr.style.height = "33px";

        // first <td> with user_id
        tdID = tr.insertCell(0);

        // input field for user_id
        inputID = document.createElement("input");
        inputID.type = "text";
        inputID.value = "";
        inputID.size = 38;
        inputID.required = true;
        inputID.pattern = "[0-9A-z]{8}-[0-9A-z]{4}-[0-9A-z]{4}-[0-9A-z]{4}-[0-9A-z]{12}";
        inputID.title = "Type your ID in format XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX";

        // add <td> to <tr>
        tdID.appendChild(inputID);

        // second <td> with pages
        tdCheck = tr.insertCell(1);

        all = allData();
        primary = all[0].primary;
        custom = all[1].custom;
        tags = all[2].tags;
        categories = all[3].categories;

        j=0;
        // array of page types
        t = ["Primary pages", "Custom pages", "Tags", "Categories"];

        for (var o in t) {
            // create new <div> for checkboxes
            div = document.createElement("div");
            // div ID same as page type name
            div.id = t[o];
            // Create header for checkboxes
            text = document.createTextNode(t[o]);
            br = document.createElement("br");
            div.appendChild(text);
            div.appendChild(br);

            // array of all pages, tags, categories that exist in WP
            data = [primary, custom, tags, categories];

            for (var i=0; i<data[j].length; i++) {
                inputCh = document.createElement("input");
                inputCh.type = "checkbox";
                inputCh.value =  data[j][i];
                inputCh.addEventListener("change", function() {
                    disEnabChbox();
                });

                // create page title
                text = document.createTextNode(data[j][i]);
                br = document.createElement("br");

                div.appendChild(inputCh);
                div.appendChild(text);
                div.appendChild(br);
            }
            j++;
            tdCheck.appendChild(div);
        }

        // third <td> with remove button
        tdRemove = tr.insertCell(2);

        // create remove button
        button = document.createElement("input");
        button.type = "button";
        button.value = "Remove";
        button.style.visibility = "hidden";

        // remove <tr> when user click on button
        button.onclick = (function() {
            tr.parentNode.removeChild(tr);
            // disEnabSubmit();
            document.getElementById("stat").innerHTML = "";
        });

        // add button to <td>
        tdRemove.appendChild(button);

        // show remove button when user hover on row
        tr.addEventListener("mouseenter", function() {
            button.style.visibility = "visible";  
        });

        // hide remove button when user move out row
        tr.addEventListener("mouseleave", function() {
            button.style.visibility = "hidden";
        });

        // disEnabSubmit();
        disEnabChbox();
    });

    // check to disable or enable submit button
    function disEnabSubmit() {
        // if there no user_id field then Submit button is disabled
        if(table.rows.length === 0) {
            submitBtn.disabled = true;
        } else {
            submitBtn.disabled = false;
        }
    }

    // check that all field of row are filled
    // return true|false
    function checkRow(row) {

        var tdID = row.getElementsByTagName("td")[0].getElementsByTagName("input")[0];
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

        // if row empty or checkboxes not checked.
        // true - all fields are filled
        // false - some fields are not filled
        if(tdID.value !== "" && ch) {
            return true;
        } else {
            return false;
        }
    }

    // add event listeners to table rows generated by PHP
    function addListeners() {

        var tdCheck, tdRemove, button, tr;

        // iterate over rows
        for(var i=0; i<table.rows.length; i++) {

            // current table row
            row = table.rows[i];

            // get <td> with checkboxes
            tdCheck = row.cells[1];
            div_arr = tdCheck.getElementsByTagName("div");
            // iterate over divs
            for(var j=0; j<div_arr.length; j++) {
                // get list of checkboxes
                ch_arr = div_arr[j].getElementsByTagName("input");
                for(var l=0; l<ch_arr.length; l++) {
                    // add  "onchange" listener to checkbox
                    ch_arr[l].addEventListener("change", function() {
                        disEnabChbox();
                    });
                }
            }

            // create third <td>
            tdRemove = row.insertCell(2);

            // create remove button
            button = document.createElement("input");
            button.type = "button";
            button.value = "Remove";
            button.style.visibility = "hidden";

            button.onclick = (function() {
                tr = this.parentNode.parentNode;
                table.tBodies[0].removeChild(tr);
                // disEnabSubmit();
                disEnabChbox();
                document.getElementById("stat").innerHTML = "";
            });

            // add button to <td>
            tdRemove.appendChild(button);

            // show remove button when user hover on row
            row.addEventListener("mouseenter", function() {
                this.cells[2].getElementsByTagName("input")[0].style.visibility = "visible";
            });

            // hide remove button when user move out from row
            row.addEventListener("mouseleave", function() {
                this.cells[2].getElementsByTagName("input")[0].style.visibility = "hidden";
            });
        }
    }

    // create object of lists of pages, tags, categories
    function allData() {
        <?php 
            $tags = get_tags( array( 'hide_empty' => false ) );
            $tags_arr = array(); 
            foreach ( $tags as $tag ) {
                array_push( $tags_arr, $tag->name );
            }

            $categories = get_categories();
            $cat_arr = array();
            if( $categories ) {
                foreach ( $categories as $cat ) {
                    array_push( $cat_arr, $cat->name );
                }
            }

            $args = array( 'post_type' => 'page' ); 
            $page_arr = array();
            $pages = get_pages( $args );
            if( $pages ) {
                foreach ( $pages as $page ) {
                    array_push( $page_arr, $page->post_title );
                }
            }

            $json = array(
                array( 'primary' => array('Main Page', 'Front Page', 'Blog Page', 'Posts') ),
                array( 'custom' => $page_arr ),
                array( 'tags' => $tags_arr ),
                array( 'categories' => $cat_arr )
            );
        ?>

        return <?php echo json_encode($json); ?>;
    }

    // get array of checkboxes names that are checked
    // return array
    function getCheckedBoxes() {
        
        var checkedArr = [];

        for(var i = 0; i < table.rows.length; i++) {

            // current table row
            row = table.rows[i];

            // get <td> with checkboxes
            tdCheck = row.cells[1];
            div_arr = tdCheck.getElementsByTagName("div");

            for(var j = 0; j < div_arr.length; j++) {
                // get list of checkboxes
                ch_arr = div_arr[j].getElementsByTagName("input");
                for(var l = 0; l < ch_arr.length; l++) {
                    // if chbox checked - add to array his name
                    if(ch_arr[l].checked) checkedArr.push(ch_arr[l].defaultValue);   
                }
            }
        }

        return checkedArr;
    }

    // disable/enable checkboxes
    function disEnabChbox() {

        var checkedArr, row, tdCheck, div_arr, ch_arr, ch_name; 

        checkedArr = getCheckedBoxes();

        for(var i=0; i<table.rows.length; i++) {

            // current table row
            row = table.rows[i];

            // get <td> with checkboxes
            tdCheck = row.cells[1];
            div_arr = tdCheck.getElementsByTagName("div");

            for(var j=0; j<div_arr.length; j++) {

                // get list of checkboxes
                ch_arr = div_arr[j].getElementsByTagName("input");

                for(var l=0; l<ch_arr.length; l++) {

                    ch_name = ch_arr[l].defaultValue;

                    if( checkedArr.indexOf(ch_name) !== -1 && !ch_arr[l].checked ) {
                        ch_arr[l].disabled = true;   
                    } else {
                        ch_arr[l].disabled = false;
                    }
                }
            }
        }
    }

</script>

</div>