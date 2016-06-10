<?php
namespace com\vital\subscription_bar;

use PDO;
use PDOException;

class DB {

	/**
	 * Make WP table name
	 *
	 * @return string
     */
	function make_table_name() {

		global $wpdb;

		// get WP's table preffix
		$table_prefix = $wpdb->prefix;
		$t_name = "subform_settings";

		// table name
		$table_name = $table_prefix . $t_name;

		return $table_name;
	}

	/**
	 * Create table in WP DB
     */
	function create_table() {

		global $wpdb;

		$table_name	= $this->make_table_name();

		// if table not exist
		$table = $wpdb->get_var(
			$wpdb->prepare( "SHOW TABLES LIKE '$table_name'", "%s" )
		);

		if ( $table != $table_name) {

			// create table
			$sql = "CREATE TABLE {$table_name} (
				id 			int(1) 	DEFAULT 1,
				user_id		text 	NOT NULL,
				pages 		text 	NOT NULL
			);";

			// we need 'upgrade.php' for dbDelta
			require_once ( ABSPATH . 'wp-admin/includes/upgrade.php' );

			// execute statement
			dbDelta( $sql );
		}
	}

	/**
	 * Save settings into WP DB for connection to another DB
	 *
	 * @param $dbtype
	 * @param $server
	 * @param $dbname
	 * @param $dbtable
	 * @param $dbuser
	 * @param $dbpass
	 * @return false|int
     */
	function save_settings($user_id) {

		// this table must have only one row
		// before insert data we need to check that table is empty
		// if not, we need to update existing row

		global $wpdb;

		$table_name = $this->make_table_name();

		// get row from table
		$data = $wpdb->get_row(
			"SELECT user_id FROM {$table_name}",
			ARRAY_A, 0
		);

		// if table empty
		if ($data == null) {
			// insert existing row
			return $wpdb->insert(
				$table_name,
				array(
					'user_id' => $user_id
				),
				array('%s')
			);
		} else {
			// update row
			return $wpdb->update(
				$table_name,
				array(
					'user_id' => $user_id
				),
				array( 'id' => 1 ),
				array('%s')
			);
		}
	}

	/**
	 * Get data form WP DB
	 *
	 * @return array|null|object|void
     */
	function get_settings() {

		global $wpdb;

		$table_name = $this->make_table_name();

		return $wpdb->get_row( "SELECT * FROM {$table_name}", ARRAY_A, 0 );
	}

	/**
	 * Insert username, email to DB table
	 *
	 * @param $username
	 * @param $email
	 * @return string
	 */
	public function insert_user_data($username, $email ) {

		global $wpdb;

		$table = $this->make_table_name();

		//get data from WP DB
		$data = $wpdb->get_row(
			"SELECT dbtype, server, dbname, dbtable, dbuser, dbpass, user_id FROM {$table}",
			ARRAY_A, 0
		);

		// create connection to another DB
		$conn = null;
		try {
			$conn = new PDO(
				"{$data['dbtype']}:host={$data['server']};dbname={$data['dbname']}",
				$data['dbuser'],
				$data['dbpass']);
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			return "Oops! Some problems with DB.";
		}

		// check that email not already exist
		$sql = "SELECT email FROM {$data['dbtable']} WHERE email='".$email."'";
		$rowCount = $conn->query( $sql )->rowCount();

		if( $rowCount == 0 ) {
			$sql = "INSERT INTO {$data['dbtable']} (username, email) VALUES ( '".$username."', '".$email."' )";
			try {
				// insert data
				$conn->exec($sql);
			} catch ( PDOException $e) {
				return "Oops! Impossible to insert information";
			}
		} else {
			return "Oops! This email already exist.";
		}

		// close connection to DB
		$conn = null;

		return "Your data has been successfully saved.";
	}

	/**
	 *	Drop table before uninstall.
     */
	public function drop_table() {

		global $wpdb;

		$table = $this->make_table_name();

		$sql = "DROP TABLE {$table}";
		$wpdb->query( $sql );
	}
}