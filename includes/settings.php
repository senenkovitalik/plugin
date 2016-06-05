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
            <td>Database type</td>
            <td><input type="text" id="dbtype" title="MySQL, MS SQL, Oracle" value="<?php echo $s['dbtype']; ?>" required /></td>
        </tr>
        <!-- <tr>
            <td>Server address</td>
            <td><input type="text" id="server" title="Server address" value="<?php echo $s['server']; ?>" required /></td>
        </tr>
        <tr>
            <td>DB name</td>
            <td><input type="text" id="dbname" title="DB name" value="<?php echo $s['dbname']; ?>" required /></td>
        </tr> -->
        <tr>
            <td>Table name</td>
            <td><input type="text" id="dbtable" title="DB table" value="<?php echo $s['dbtable']; ?>" required /></td>
        </tr>
        <tr>
            <td>DB user</td>
            <td><input type="text" id="dbuser" title="DB user" value="<?php echo $s['dbuser']; ?>" required /></td>
        </tr>
        <tr>
            <td>DB password</td>
            <td><input type="password" id="dbpass" title="DB password" value="<?php echo $s['dbpass']; ?>"  required /></td>
        </tr>
        <tr>
            <td>User ID</td>
            <td><input type="text" id="user-id" title="User ID" value="<?php echo $s['user_id']; ?>" required></td>
        </tr>
        <tr>
            <td colspan="2">
                <input type="submit" value="Submit" class="button button-primary">
                <span id="stat" style="vertical-align: middle; margin-left: 20px; color: #00ff00;"></span>
            </td>
        </tr>
    </table>
</form>
