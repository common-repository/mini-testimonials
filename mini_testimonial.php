<?php
/*
Plugin Name: Mini Testimonials
Plugin URI: http://wordpress.org/plugins/mini-testimonials
Description: Create and display rotating testimonials without creating their own page
Version: 1.0.0
Author: Ellytronic Media
Author URI: http://ellytronic.com
License:GPL2
*/

/* Copyright 2013  Ellytronic Media  (email : iwork@ellytronic.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/**
* Gets a random testimonial
* and publishes it with shortcode
* [etm_mt]
* @since 0.9.0
*/
function etm_mt_print_testimonial() {
		global $wpdb;
		//get the row as an object
		$table_name = $wpdb->prefix . "etm_mt";
		$obj = $wpdb->get_row("SELECT * FROM {$table_name} ORDER BY RAND() ASC LIMIT 1");

		//print the row		
		$testimonial = '<p class="mt-quote">' . $obj->testimonial . '</p>';		
		if( trim( $obj->author_url ) != "") {
			$obj->author_url = urldecode($obj->author_url);
			$testimonial .= "<p class='mt-quote-source-name'>&mdash;<a href='http://" . $obj->author_url . "' taget='_blank'>" . $obj->author_name ."</a></p>";
			if( trim( $obj->author_company) != "" ) $testimonial .= "<p class='mt-quote-source-company'><a href='http://" . $obj->author_url . "' taget='_blank'>" . $obj->author_company ."</a></p>";
		} else {
			$testimonial .= "<p class='mt-quote-source'>&mdash;" . $obj->author_name ."</p>";
			if( trim( $obj->author_company) != "" ) $testimonial .= "<p class='mt-quote-source-company'>". $obj->author_company ."</p>";
		}
		return $testimonial;
}
add_shortcode( 'etm_mt', 'etm_mt_print_testimonial' ); 


/**
* installs the plug-in
* @since 0.9.0
*/
function etm_mt_install() {
	global $wpdb;

	//included required files
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );	

	//create the form table
	//finicky SQL, read https://codex.wordpress.org/Creating_Tables_with_Plugins for details
	$table_name = $wpdb->prefix . "etm_mt";
	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
	  id mediumint(5) NOT NULL AUTO_INCREMENT,	  
	  testimonial text NOT NULL,
	  author_name tinytext NOT NULL,
	  author_company tinytext NOT NULL,
	  author_url text NOT NULL,	  
	  UNIQUE KEY id (id)
	);";

	dbDelta( $sql );
	
	add_option( "etm_mt_db_version", "0.9.1" );

}
register_activation_hook( __FILE__, 'etm_mt_install' );


/**
* load the admin scripts
* @since 0.9.0
*/

function etm_mt_admin_scripts() {
	if( 'etm-testimonials' != $_GET['page'] )
    	return;
	wp_enqueue_script( "etm_mt", plugins_url( "admin_menu.js", __FILE__ ), array("jquery") );
}
add_action('admin_enqueue_scripts', 'etm_mt_admin_scripts');


/**
* adds the plugin to menu and queues the admin scripts
* @since 0.9.0
*/
function etm_mt_register_admin_menu() {
	//add to the menu	
	add_menu_page( "Ellytronic Testimonials", "Mini Testimonials", "manage_options", "etm-testimonials", "etm_mt_display_admin_menu" );
	#add_submenu_page('edit.php?post_type=page', 'Testimonials', 'Testimonials', 'manage_options', 'etm-testimonials', 'etm_mt_display_admin_menu');	
}
add_action( 'admin_menu', 'etm_mt_register_admin_menu' );


/**
* display the admin form
* @since 0.9.0
*/
function etm_mt_display_admin_menu() {
	require ('admin_menu.php');
}


/**
* ajax function to update the form
* @since 0.9.0
*/
function etm_mt_update_form() {
	global $wpdb;
	
	//fetch the data
	$data = $_POST['data'];

	//prepare our master array
	$newData = array();

	//clean out the database;
	$table_name = $wpdb->prefix . "etm_mt";
	$wpdb->query("DELETE FROM {$table_name}");

	//turn out each value separately
	foreach($data as $key=>$val) {		
		//explode the data into array format
		$details = explode("|+etm+|", $val);
		$newData[$key] = array(
				"testimonial" => $details[0],
				"author_name" => $details[1],
				"author_company" => $details[2],	 			
				"author_url" => urlencode($details[3])
			);
		//update the database
		$wpdb->insert($table_name, $newData[$key]);
		unset($details);
	}

	echo "true";
	die();

}
add_action('wp_ajax_etm_mt_update_form', 'etm_mt_update_form');

?>