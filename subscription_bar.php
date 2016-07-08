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
		add_action( 'wp_ajax_popup_action', array( $this, 'popup_action_close' ) );
		add_action( 'wp_ajax_rate_action', array( $this, 'rate_action_close' ) );

		// JS for popup message
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_popup_message' ));
		add_action( 'admin_enqueue_scripts', array( $this, 'rate_plugin' ));

		// admin settings page
		add_action( 'admin_menu', array( $this, 'admin_add_menu_item' ));

		// admin settings page CSS
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_style' ));

		// add JS to pages
		add_action('wp_footer', array( $this, 'insert_script'));

		// Show popup #2 after 2 weeks
		add_action('two_weeks_event', array( $this, 'do_this_ones' ));
		// Show popup #2 after 1 week
		add_action('one_week_event', array( $this, 'do_this_ones' ));

		// Show rate message
		add_action('admin_footer', array( $this, 'rate_plugin' ));

		$this->db = $db;
	}

	/**
	 *	Create table in WP DB
	 *  and add event to scheduler
     */
	public static function activate() {
		$db = new DB();
		$db->create_table();
	}

	// Clear schedule list from all tasks
	public static function deactivate() {
		wp_clear_scheduled_hook('two_weeks_event');
		wp_clear_scheduled_hook('one_week_event');
	}

	// Add JS to admin page for AJAX
	function admin_enqueue_ajax() {
		wp_enqueue_script( 'bar-admin-ajax', plugins_url( '/assets/js/admin_ajax.js', __FILE__), array('jquery') );
		wp_localize_script( 'bar-admin-ajax', 'admin_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}

	// Add event to scheduler - show popup #2 after 2 weeks
	// Save setting to DB
	function admin_action_callback() {
		$s = false;
		// get data from settings page
		$data = isset( $_POST['data'] ) ? $_POST['data'] : false;

		if ( $data ) {
			// if there at least one user ID
			foreach ( $data as $d ) {
				if ( isset( $d['user_id'] ) ) {
					$s = true;
				}
			}
			if ( $s ) {
				// show popup #2 after 2 weeks
				wp_schedule_single_event( time() + 3600*24*14, 'two_weeks_event' );
			}
		}
		
		// try to save data to DB
		if(	$this->db->save_settings( $data ) ) {
			echo "Your data succesfuly saved!!!";
		} else {
			echo "Oops! Some problems.";
		}

		wp_die();
	}

	// Add option for show/hide popup #1
	function popup_action_close() {
		$data = $_POST['close'];

		if ($data) {
			add_option('show_popup', 'no');
		}

		wp_die();
	}

	// Add option for show/hide popup #2
	// hide forever or only for one week
	function rate_action_close() {
	
		$close_forever = isset($_POST['closeForever']) ? $_POST['closeForever'] : false;
		$close_for_a_week = isset($_POST['closeForAWeek']) ? $_POST['closeForAWeek'] : false;

		if ( $close_forever ) {
			delete_option('rate_mes');
		}

		if ( $close_for_a_week ) {
			delete_option('rate_mes');
			wp_schedule_single_event( time() + 3600*24*7, 'one_week_event' );
		}

		wp_die();
	}

	// Show popup #1 at all admin's pages
	function admin_popup_message() {
		$show = get_option('show_popup');
		// uncoment line after and comment out line before to show popup #1
		// $show = "yes";

		if ( $show !== "no" ) {
			wp_register_script( 'popup_message', plugins_url('/assets/js/popup.js', __FILE__) );
			wp_enqueue_script( 'popup_message' );
		}
	}

	// Add menu item to admin page
	function admin_add_menu_item() {

	  // create global var for ident setting page
	  global $bar_settings_page;

	  $bar_settings_page =  add_menu_page(
	    'Subscription Bar', 
		'Subscript Bar settings', 
		'manage_options', 
		'subscription_bar', 
		array( $this, 'show_settings_page')
	  );
	}

	// Add CSS file to settings page
	function admin_enqueue_style($hook) {

	  // hook of settings page
	  global $bar_settings_page;

	  if ( $bar_settings_page != $hook ) {
		return;
	  }

	  wp_register_style( 'bar_style', plugins_url( '/assets/css/bar_style.css', __FILE__ ) );
	  wp_enqueue_style( 'bar_style' );
	}

	function insert_script() {

		$post = get_post();

		// blog page
		if ( is_front_page() && is_home() ) {
			echo "<script>console.log('The Blog Page')</script>";
			$current_page = "Blog Page";
		// front page
		} elseif ( is_front_page() ) {
			echo "<script>console.log('The Front Page')</script>";
			$current_page = "Front Page";
		// main page
		} elseif ( is_home() ) {
			echo "<script>console.log('The Main Page')</script>";
			$current_page = "Main Page";
		// page
		} elseif ( is_page() ) {
			$current_page = $post->post_title;
			echo "<script>console.log('Everything else : Page $current_page')</script>";
		// post
		} elseif ( is_single() ) {
			$current_page = "Posts";
			echo "<script>console.log('Everything else : Post $current_page')</script>";
		// category
		} elseif ( is_category() ) {
			$current_page = single_cat_title( $prefix = '', $display = false );
			echo "<script>console.dir('Everything else : Category $current_page')</script>";
		// tag
		} elseif ( is_tag() ) {
			$current_page = single_tag_title( $prefix = '', $display = false );
			echo "<script>console.log('Everything else : Tag $current_page')</script>";
		}

		// find user_id by post name
		$data_widget_id = $this->db->find_user_id( $current_page );

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

			var code = "."'(function() {function async_load(){ var s = document.createElement(\"script\"); s.type = \"text/javascript\"; s.async = true; var theUrl = \"https://rabbut.com/api/v1/js/$data_widget_id\"; s.src = theUrl + ( theUrl.indexOf(\"?\") >= 0 ? \"&\" : \"?\") + \"ref=\" + encodeURIComponent(window.location.href);	var embedder = document.getElementById(\"rabbut-o-matic\");	embedder.parentNode.insertBefore(s, embedder); } if (window.attachEvent) { window.attachEvent(\"onload\", async_load); } else {	window.addEventListener(\"load\", async_load, false);}})();'"."

			// create text node with text of new script and add it to new prev script
			script.appendChild( document.createTextNode(code) );

			// insert new <script> after open <body> tag
			body.insertBefore(script, body.childNodes[0]);
		</script>";

		echo $output;
	}

	// Add option that indicates show popup #2 or not
	function do_this_ones() {
		add_option('rate_mes', '1', '', 'yes');
	}

	// Show popup #2
	function rate_plugin() {
		$opt = intval( get_option('rate_mes') );

		if ($opt === 1) {
			wp_register_script( 'rate_message', plugins_url('/assets/js/rate_popup.js', __FILE__) );
			wp_enqueue_script( 'rate_message' );
		}
	}

	// Show settings page
	function show_settings_page() {
		require_once "includes/settings.php";
	}
}

register_activation_hook( __FILE__, array( 'SubscriptionBar', 'activate' ));
register_deactivation_hook( __FILE__, array( 'SubscriptionBar', 'deactivate' ));

new SubscriptionBar( new DB() );