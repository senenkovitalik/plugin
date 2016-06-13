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

	// Save user id's
	function save_settings( $data ) {

		global $wpdb;

		// get table name 
		$table_name = $this->make_table_name();
		// clear table
		$wpdb->query("TRUNCATE TABLE {$table_name};");

		$pages = "";

		for ($i=0; $i<count($data); $i++) { 
			$user_id = $data[$i]['user_id'];
			foreach ($data[$i]['pages'] as $p) {
				$pages .= $p.","; 
			}

			$status = $wpdb->insert(
				$table_name,
				array(
					'user_id' => $user_id,
					'pages' => $pages
				),
				array('%s', '%s')
			);

			$pages = "";
		}

		return $status;
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
	 *	Drop table before uninstall.
     */
	public function drop_table() {

		global $wpdb;

		$table = $this->make_table_name();

		$sql = "DROP TABLE {$table}";
		$wpdb->query( $sql );
	}

	// get user_id value assigned to some page
	function find_user_id($page) {

		$data = $this->get_data();

		foreach ($data as $row) {

            // remove all whitespaces
            $pages = preg_replace('/\s+/', '', $row['pages']);
            // brake string into array
            $pages_arr = explode(',', $pages);

            foreach ($pages_arr as $p) {
            	// return $p ." ". $page;
                if($p === $page) {
                	return $row['user_id'];
                }
            }
        }
        return "";
	}
}