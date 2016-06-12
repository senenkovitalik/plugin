<?php

/*
Plugin Name: Subscription Bar
Plugin URI:
Description: This plugin allow you to collect users email data and save it to your server.
Version: 1.0
Author: Vitaliy Senenko
Author URi:
*/

/*  Copyright 2015  Vitaliy Senenko  (email: senenkovitalik@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

use \com\vital\subscription_bar\DB;

require_once "includes/db.php";

class SubscriptionBar {

	protected $db;

	public function __construct( DB $db ) {

		// add theme CSS files
	 	add_action( 'wp_enqueue_scripts', array( $this, 'add_styles' ) );

		// add theme JS file
		add_action( 'wp_enqueue_scripts', array( $this, 'add_js'));

		// ajax admin
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_ajax' ) );
		add_action( 'wp_ajax_admin_action', array( $this, 'admin_action_callback' ) );

		// ajax theme
		add_action( 'wp_enqueue_scripts', array( $this, 'theme_enqueue_ajax' ) );
		add_action( 'wp_ajax_action', array( $this, 'theme_action_callback' ) );
		add_action( 'wp_ajax_nopriv_action', array( $this, 'theme_action_callback' ) );

		// admin settings page
		add_action( 'admin_menu', array( $this, 'admin_add_menu_item' ) );

		$this->db = $db;
	}

	/**
	 *	Create table in WP DB for settings
     */
	public static function activate() {

		$db = new DB();
		$db->create_table();
	}

	/**
	 * Register CSS files
     */
	function add_styles() {
		wp_register_style( 'bar-theme-style', plugins_url( '/assets/css/style.css', __FILE__ ) );
		wp_enqueue_style( 'bar-theme-style' );
	}

	/**
	 *	Add JavaScript file
     */
	function add_js() {
		wp_enqueue_script( 'bar-theme-bar', plugins_url( '/assets/js/bar.js', __FILE__ ), array('jquery') );
	}

	/**
	 * Add JS to admin page for AJAX
     */
	function admin_enqueue_ajax() {
		wp_enqueue_script( 'bar-admin-ajax', plugins_url( '/assets/js/admin_ajax.js', __FILE__), array('jquery') );
		wp_localize_script( 'bar-admin-ajax', 'admin_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}

	/**
	 *	Add JS file, and pass path to 'admin-ajax.php' to them
	 */
	function theme_enqueue_ajax() {
		wp_enqueue_script( 'bar-ajax-script', plugins_url( '/assets/js/ajax.js', __FILE__), array('jquery') );
		wp_localize_script( 'bar-ajax-script', 'svi_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}

	/**
	 *	Insert user data to DB table and return response
     */
	function theme_action_callback() {

		$username = $_POST['username'];
		$email = $_POST['email'];

		$status = $this->db->insert_user_data( $username, $email );

		// response to ajax.js
		echo $status;

		wp_die();
	}

	/**
	 * Save setting for another DB to WP DB
     */
	function admin_action_callback() {

		$json = $_POST['data'];

		$pages = "";

		for ($i=0; $i<count($json); $i++) { 
			$user_id = $json[$i]['user_id'];
			foreach ($json[$i]['pages'] as $p) {
				$pages .= $p.","; 
			}

			$this->db->save_settings( $user_id, $pages );

			$pages = "";
		}

		// $status = $this->db->save_settings( $user_id, $pages );

		// if ( $status === false ) {
		// 	echo "Some problems occur";
		// } else if ( $status === 0) {
		// 	echo "Nothing update";
		// } else if ( $status === true) {
		// 	echo "Your data successfully saved.";
		// } else {
		// 	echo "Your data successfully updated.";
		// }

		wp_die();
	}

	/**
	 * Add menu item to admin page
     */
	function admin_add_menu_item() {
		add_menu_page(
			'Subscription Bar', 
			'Subscript Bar settings', 
			'manage_options', 
			'subscription_bar', 
			array( $this, 'show_settings_page')
			);
	}

	/**
	 *	Show settings page
     */
	function show_settings_page() {
		require_once "includes/settings.php";
	}
}

register_activation_hook( __FILE__, array('SubscriptionBar', 'activate') );

new SubscriptionBar( new DB() );