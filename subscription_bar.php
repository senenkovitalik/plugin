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

		// ajax admin
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_ajax' ) );
		add_action( 'wp_ajax_admin_action', array( $this, 'admin_action_callback' ) );

		// admin settings page
		add_action( 'admin_menu', array( $this, 'admin_add_menu_item' ) );

		// add JS to pages
		add_action('wp_head', array( $this, 'insert_script'));

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
	 * Add JS to admin page for AJAX
     */
	function admin_enqueue_ajax() {
		wp_enqueue_script( 'bar-admin-ajax', plugins_url( '/assets/js/admin_ajax.js', __FILE__), array('jquery') );
		wp_localize_script( 'bar-admin-ajax', 'admin_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}

	/**
	 * Save setting for another DB to WP DB
     */
	function admin_action_callback() {

		$data = $_POST['data'];
		$this->db->save_settings( $data );

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

	function insert_script() {

		// current post name
		$page = get_post()->post_name;

		// find user_id by post name
		$id = $this->db->find_user_id($page);

		$output = 
		"<script> 
			alert('user-data-id: $id');
		</script>";
		echo $output;
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