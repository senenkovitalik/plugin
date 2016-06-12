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
				id 			int 	NOT NULL AUTO_INCREMENT,
				user_id		text 	NOT NULL,
				pages 		text 	NOT NULL,
				PRIMARY KEY (id)
			);";

			// we need 'upgrade.php' for dbDelta
			require_once ( ABSPATH . 'wp-admin/includes/upgrade.php' );

			// execute statement
			dbDelta( $sql );
		}
	}

	// Dave user id's
	function save_settings( $data ) {

		global $wpdb;

		// get table name 
		$table_name = $this->make_table_name();

		$wpdb->query("TRUNCATE TABLE {$table_name};");

		$pages = "";

		for ($i=0; $i<count($data); $i++) { 
			$user_id = $data[$i]['user_id'];
			foreach ($data[$i]['pages'] as $p) {
				$pages .= $p.","; 
			}

			$wpdb->insert(
				$table_name,
				array(
					'user_id' => $user_id,
					'pages' => $pages
				),
				array('%s', '%s')
			);

			$pages = "";
		}
	}

	/**
	 * Get data form WP DB
	 *
	 * @return array|null|object|void
     */
	function get_data() {

		global $wpdb;

		$table_name = $this->make_table_name();

		// array of row 
		$data = $wpdb->get_results("SELECT * FROM {$table_name};", ARRAY_A);

		return $data;
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