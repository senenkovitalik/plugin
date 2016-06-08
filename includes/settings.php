<?php

/**
 * Created by PhpStorm.
 * User: user
 * Date: 01.01.16
 * Time: 23:52
 */
?>

<h2>Subscription bar settigs</h2>
<p>You need to add informattion for access to DB</p>

<?php

    require_once "db.php";

    $s = $this->db->get_settings();
?>

<form id="form_db_set">
    <table>
        <tr>
            <td colspan="2">If you don't have account yet, please <a href="https://rabbut.com/">visit</a> our web-site.</td>
        </tr>
        <tr>
            <td>User ID</td>
            <td><input type="text" id="user_id" title="User ID" value="<?php echo $s['user_id']; ?>"  size="40" required></td>
        </tr>
        <tr>
            <td>use for</td>
            <td>
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
            <!-- Add dropdown list of pages -->
            <select name="pages">
                <option value="front_page">Front page</option>
                <?php
                    foreach($pages as $p) {
                        echo "<option value='$p->post_name'>$p->post_title</option>";
                    }
                ?>
            </select>  
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" value="Submit" class="button button-primary">
                <span id="stat" style="vertical-align: middle; margin-left: 20px; color: #00ff00;"></span>
            </td>
        </tr>
    </table>
</form>
