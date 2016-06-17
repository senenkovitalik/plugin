<?php

/*
Plugin Name: Subscription Bar
Plugin URI:
Description: This plugin allow you to add subscription forms to pages you want. <a href="admin.php?page=subscription_bar">Go to settings page</a>
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
		add_action('wp_footer', array( $this, 'insert_script'));

		add_action('wp_footer', array($this, 'ident_page' ));

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
	 * Save setting DB to WP DB
     */
	function admin_action_callback() {

		// get data from settings page
		$data = $_POST['data'];

		// try to save data to DB
		if(	$this->db->save_settings($data) ) {
			echo "Your data succesfuly saved!!!";
		} else {
			echo "Oops! Some problems.";
		}

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
		$data_widget_id = $this->db->find_user_id($page);

		// if no id for this page - exit
		if($data_widget_id == "") {
			return;
		}

		$output = 
		"<script>
			// get <body> tag
			var body = document.getElementsByTagName(\"body\")[0];
			// create new <script> tag
			var script = document.createElement(\"script\");
			script.setAttribute(\"data-version\", \"v1\");
			script.setAttribute(\"data-widget-id\", \"$data_widget_id\");
			script.id = \"rabbut-o-matic\";
			script.type = \"text/javascript\";

			var code = "."'(function() {function async_load(){ var s = document.createElement(\"script\"); s.type = \"text/javascript\"; s.async = true; var theUrl = \"https://devvy.rabbut.com/api/v1/js/$data_widget_id\"; s.src = theUrl + ( theUrl.indexOf(\"?\") >= 0 ? \"&\" : \"?\") + \"ref=\" + encodeURIComponent(window.location.href);	var embedder = document.getElementById(\"rabbut-o-matic\");	embedder.parentNode.insertBefore(s, embedder); } if (window.attachEvent) { window.attachEvent(\"onload\", async_load); } else {	window.addEventListener(\"load\", async_load, false);}})();'"."

			// create text node with text of new script and add it to new prev script
			script.appendChild( document.createTextNode(code) );

			// insert new <script> after open <body> tag
			body.insertBefore(script, body.childNodes[0]);
		</script>";

		echo $output;
	}

	function ident_page() {
		if ( is_front_page() && is_home() ) {
			echo "<script>console.log('The Blog Page')</script>";
		} elseif ( is_front_page() ) {
			echo "<script>console.log('The Front Page')</script>";
		} elseif ( is_home() ) {
			echo "<script>console.log('The Main Page')</script>";
		} else {
			if ( is_page() ) {
				echo "<script>console.log('Everything else : Page')</script>";
			} elseif ( is_single() ) {
				echo "<script>console.log('Everything else : Post')</script>";
			} elseif ( is_category() ) {
				echo "<script>console.log('Everything else : Category')</script>";
			} elseif ( is_tag() ) {
				echo "<script>console.log('Everything else : Tag')</script>";
			}
		}
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