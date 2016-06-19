<?php
namespace com\vital\subscription_bar;

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

		// check that table already exist
		$table = $wpdb->get_var(
			$wpdb->prepare( "SHOW TABLES LIKE '$table_name'", "%s" )
		);

		if ( $table !== $table_name) {

			// create table
			$sql = "CREATE TABLE {$table_name} (
				id 				int 		 NOT NULL AUTO_INCREMENT,
				user_id	 		varchar(255) NOT NULL,
				primary_pages 	varchar(255),
				custom_pages 	varchar(255),
				tags 			varchar(255),
				categories 		varchar(255),
				PRIMARY KEY (id)
			);";

			// execute statement
			$stat = $wpdb->query( $sql );
		}
	}

	// Save user data to DB
	function save_settings( $data ) {

		global $wpdb;

		// get table name 
		$table_name = $this->make_table_name();
		// clear table
		$wpdb->query("TRUNCATE TABLE {$table_name};");

		// iterate over objects
		foreach ($data as $d) { 

			// transform arrays into strings separated by comma
			$user_id = $d['user_id'];
			$primary_pages = implode(", ", $d['primary_pages']);
			$custom_pages  = implode(", ", $d['custom_pages']);
			$tags 		   = implode(", ", $d['tags']);
			$categories    = implode(", ", $d['categories']);

			$status = $wpdb->insert(
				$table_name,
				array(
					'user_id' 		=> $user_id,
					'primary_pages' => $primary_pages,
					'custom_pages'	=> $custom_pages,
					'tags'			=> $tags,
					'categories'	=> $categories
				),
				array('%s', '%s', '%s', '%s', '%s')
			);
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
	function find_user_id( $page ) {

		$data = $this->get_data();

		echo "<script> console.log('Page : $page'); </script>";

		foreach ($data as $row) {

            // brake string into array
            $prim = explode(', ', $row['primary_pages']);
            $cust = explode(', ', $row['custom_pages']);
            $tags = explode(', ', $row['tags']);
            $cats = explode(', ', $row['categories']);

            if( in_array($page, $prim) || in_array($page, $cust) ||	in_array($page, $tags) || in_array($page, $cats) ) {
            		return $row['user_id'];
            }
        }
        return "";
	}
}