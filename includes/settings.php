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
            <td colspan="2">If you don't have account yet, please <a href="https://rabbut.com/">visit</a> out site.</td>
        </tr>
        <tr>
            <td>User ID</td>
            <td><input type="text" id="user_id" title="User ID" value="<?php echo $s['user_id']; ?>"  size="40" required></td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" value="Submit" class="button button-primary">
                <span id="stat" style="vertical-align: middle; margin-left: 20px; color: #00ff00;"></span>
            </td>
        </tr>
    </table>
</form>
