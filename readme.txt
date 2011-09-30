=== Old Custom Fields ===
Contributors: Akifumi Nishikawa
Donate link: http://www.oldoffice.com/
Tags: custom field,multisite,resize image
Requires at least: 3.21ja
Tested up to: 1.1.5
Stable tag: 1.1.5

	original custom fields setting plugin.
	

== Description ==

	original custom fields setting plugin.
	for multisite.


== Installation ==

1. Upload "old-custom-fields" to the "/wp-content/plugins/" directory
2. Activate the plugin through the "Plugins" menu in WordPress
3. Please setup each fields through the "Setting > Custom Field" menu in WordPress


== Changelog ==

	= 1.1.5 =
	* create thumbnail error -> repair
	* changed includes/ocf_post.php, old-custom-fields.php
	* 
	* PHP 5.*
	* WordPress 3.21ja

	= 1.1.4 =
	* case post_type = @setup page : 'input_name' can not use '-' && '_'
	* changed ocf_setup.js, old-custom-fields.php
	* 
	* PHP 5.*
	* WordPress 3.13ja

	= 1.1.3 =
	* case post_type = 'page' : disp error customfields -> repair
	* changed old-custom-fields.php line 130
	* 
	* PHP 5.*
	* WordPress 3.13ja

	= 1.1.2 =
	* setup page : same input name -> error, 2bite input name error, same input name error
	* changed ocf_setup.class.php, ocf-setup.js
	* 
	* PHP 5.*
	* WordPress 3.12ja

	= 1.1.1 =
	* changed ocf_setup.class.php, ocf-post.class.php, old-custom-fields.php
	* new public
	* 
	* PHP 5.*
	* WordPress 3.12ja


== Upgrade Notice ==

	= 1.1.2 =
	add setup validate func. 


== Arbitrary section ==

	http://doluck.net/release/old_custom_fields/