<?php
/*
Plugin Name:  Old Custom Fields
Plugin URI:   http://doluck.net/
Description:  custom fields setting plugin for wordpress.
Version:      1.1.6　(for WordPress3.2.1ja)
Author:       Akifumi Nishiakwa
Author URI:   http://www.oldoffice.com/
*/

/*  Copyright 2011 Akifumi Nishikawa

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


## BASE SETTING

	// boxname setting
	$box_name = 'Custom Fields';
	
	// $post
	global $post;
	
	// plugin url&dir
	if( !defined( 'OCF_PLUGIN_URL' ) ) {
		define( 'OCF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	}
	if( !defined( 'OCF_PLUGIN_DIR' ) ) {
		define( 'OCF_PLUGIN_DIR', ABSPATH.'wp-content/plugins/old-custom-fields' );
	}
	
	
## INIT

	function ocf_init_options() {
	
		if( !get_option( 'ocf_installed' ) ) {
			$ocf_init_arr = array();
			update_option( 'ocf_base_data', $ocf_init_arr );
			update_option( 'ocf_installed', 1 );
		}
	}
	
	register_activation_hook( __FILE__, 'ocf_init_options' );


## SETUP PAGE
	
	//add admin menu
	add_action( 'admin_menu', 'ocf_add_admin_menu' );

	function ocf_add_admin_menu() {
	
		global $box_name;
		$ocf_setup = new ocf_add_setup_class();
		$plugin_page = add_options_page( 'Old Custom Fields', $box_name, 8, __FILE__, array( $ocf_setup, 'ocf_setup_page' ) );
		add_action( 'admin_head-'.$plugin_page, 'ocf_insert_head_setup' );
	}

	//add js&css for setup page
	function ocf_insert_head_setup() {
		
		$tag = '';
		$tag .= '<link rel="stylesheet" href="'.OCF_PLUGIN_URL.'/css/ocf_setup.css" type="text/css" media="all" />'."\n";
		$tag .= '<script type="text/javascript" src="'.OCF_PLUGIN_URL.'/js/ocf_setup.js"></script>'."\n";
		echo $tag;
	}
	
	//setup page
	include_once( OCF_PLUGIN_DIR.'/includes/ocf_setup.class.php' );
	

## PLUGIN

	/* JS & CSS setting */

	// jscss for post page
	function ocf_insert_head_post() {
		
		$tag = '';
		$tag .= '<link rel="stylesheet" href="'.OCF_PLUGIN_URL.'css/ocf_post.css" type="text/css" media="all" />'."\n";
		$tag .= '<script type="text/javascript" src="'.OCF_PLUGIN_URL.'js/cookie.js"></script>'."\n";
		$tag .= '<script type="text/javascript" src="'.OCF_PLUGIN_URL.'js/ocf_post.js"></script>'."\n";
		echo $tag;
	}
	
	if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
		add_action( 'admin_head', 'ocf_insert_head_post' );
	}
	
	// js for uploader
	function ocf_insert_head_madia_upload() {
		
		$tag = '';
		$tag .= '<script type="text/javascript" src="'.OCF_PLUGIN_URL.'js/cookie.js"></script>'."\n";
		$tag .= '<script type="text/javascript" src="'.OCF_PLUGIN_URL.'js/ocf_media_upload.js"></script>'."\n";
		echo $tag;
	}
	
	if ( in_array( $pagenow, array( 'media-upload.php' ) ) ) {
		add_action( 'admin_head', 'ocf_insert_head_madia_upload' );
	}
	
	// stop flash uploader
	function noflashuploader() {
		return false;
	}
	add_filter( 'flash_uploader', 'noflashuploader', 5 );
	
	/* DATA */

	include_once( 'includes/ocf_post.class.php' );
	include_once( ABSPATH.'wp-admin/includes/template.php' );
	$OCF_POST = new ocf_post_class();
	
	/* disp custom fields @ post page */
	$cur_post_type = ( isset( $_REQUEST[ 'post' ] ) ) ? get_post_type( $_REQUEST[ 'post' ] ) : 'post';
	add_meta_box( 'old_custom_fields', $box_name, array( &$OCF_POST, 'ocf_post_page' ), $cur_post_type, 'normal', 'high' );
	
	/* save post data */
	// (post and page) case: post-new, edit-post, add comment for post & page
	add_action( 'edit_post', array( $OCF_POST, 'edit_meta_value' ) );
	// (post and page) case: use import func, use xml-rpc, use mail post
	add_action( 'save_post', array( $OCF_POST, 'edit_meta_value' ) );
	// (post) case: publish-post
	add_action( 'publish_post', array( $OCF_POST, 'edit_meta_value' ) );
	// (page) case: transition_post_status
	add_action( 'transition_post_status', array( $OCF_POST, 'edit_meta_value' ) );

?>
