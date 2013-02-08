<?php
/*
 * Plugin Name: Enterprise
 * Description: A WordPress plugin made for professional websites.
 * Version: 0.1
 * Author: Patrick Daly
 * Author URI: http://developdaly.com
 *
 * Copyright (c) 2012 Develop Daly
 * http://developdaly.com
 */

 // Disables updating core/themes/plugins if not developing locally
add_action( 'init', 'enterprise_plugin_disable_updates' );

// Adds custom post meta boxes
add_action( 'add_meta_boxes', 'enterprise_plugin_add_meta_boxes' );

// Saves post meta boxes
add_action( 'save_post', 'enterprise_plugin_save_postdata' );


/**
 * Sets up and add custom meta boxes.
 *
 * @since 0.1.0
 */
function enterprise_plugin_add_meta_boxes( $postType ) {
	$types = array('post', 'page');
	if ( in_array( $postType, $types ) ) {
		add_meta_box( 'enterprise-css',			__( 'Custom CSS',			'enterprise'), 'enterprise_inner_css_box', $postType );
		add_meta_box( 'enterprise-javascript',	__( 'Custom JavaScript',	'enterprise'), 'enterprise_inner_javascript_box', $postType );
	}
}

/**
 * Creates the inside of the JavaScript meta box.
 *
 * @since 0.1.0
 */
function enterprise_plugin_inner_javascript_box( $post ) {

	// Use nonce for verification
	wp_nonce_field( plugin_basename(__FILE__), 'enterprise_noncename' );

	// The actual fields for data entry
	echo '<p><label for="enterprise-javascript">Custom JavaScript (<strong>to be used only on this page</strong>) will be placed at the bottom of the document after all other JS:</label></p>';
	echo '<textarea id="enterprise-javascript" name="enterprise-javascript" cols="60" rows="5" tabindex="30" style="width: 99%;">'. get_post_meta( $post->ID, 'enterprise-javascript', true ) .'</textarea>';
}

/**
 * Creates the inside of the CSS meta box.
 *
 * @since 0.1.0
 */
function enterprise_plugin_inner_css_box( $post ) {

	// Use nonce for verification
	wp_nonce_field( plugin_basename(__FILE__), 'enterprise_noncename' );

	// The actual fields for data entry
	echo '<p><label for="enterprise-css">Custom CSS (<strong>to be used only on this page</strong>) will be placed at the bottom of <code>HEAD</code> after all enqueued CSS:</label></p>';
	echo '<textarea id="enterprise-css" name="enterprise-css" cols="60" rows="5" tabindex="30" style="width: 99%;">'. get_post_meta( $post->ID, 'enterprise-css', true ) .'</textarea>';
}

/**
 * Saves the meta boxes.
 *
 * @since 0.1.0
 */
function enterprise_plugin_save_postdata( $post_id ) {
	
	// If auto-saving, don't want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;
	
	// Verify savings with proper authorization
	if ( !wp_verify_nonce($_POST['enterprise_noncename'], plugin_basename(__FILE__) ) )
		return;
	
	// Check permissions
	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can('edit_page', $post_id ) )
			return;
	} else {
		if ( !current_user_can('edit_post', $post_id ) )
			return;
	}

	// Get posted variables
	$enterprise_javascript	= $_POST['enterprise-javascript'];
	$enterprise_css			= $_POST['enterprise-css'];
	
	// Update/add post meta
	update_post_meta( $post_id, 'enterprise-javascript',	$enterprise_javascript );
	update_post_meta( $post_id, 'enterprise-css',		$enterprise_css );
}

/**
 * Disables core and plugin updates as well as notifications to update if
 * not in a local environment.
 *
 * @since 0.1.0
 */
function enterprise_plugin_disable_updates() {

	// Disable the disabling in local environments
	if( WP_LOCAL_DEV == true )
		return false;
		
	//Disable Theme Updates
	# 2.8 to 3.0:
	remove_action( 'load-themes.php', 'wp_update_themes' );
	remove_action( 'load-update.php', 'wp_update_themes' );
	remove_action( 'admin_init', '_maybe_update_themes' );
	remove_action( 'wp_update_themes', 'wp_update_themes' );
	add_filter( 'pre_transient_update_themes', create_function( '$a', "return null;" ) );
	wp_clear_scheduled_hook( 'wp_update_themes' );
	
	# 3.0:
	remove_action( 'load-update-core.php', 'wp_update_themes' );
	add_filter( 'pre_site_transient_update_themes', create_function( '$a', "return null;" ) );
	wp_clear_scheduled_hook( 'wp_update_themes' );
	
	
	//Disable Plugin Updates
	# 2.8 to 3.0:
	remove_action( 'load-plugins.php', 'wp_update_plugins' );
	remove_action( 'load-update.php', 'wp_update_plugins' );
	remove_action( 'admin_init', '_maybe_update_plugins' );
	remove_action( 'wp_update_plugins', 'wp_update_plugins' );
	add_filter( 'pre_transient_update_plugins', create_function( '$a', "return null;" ) );
	wp_clear_scheduled_hook( 'wp_update_plugins' );
	
	# 3.0:
	remove_action( 'load-update-core.php', 'wp_update_plugins' );
	add_filter( 'pre_site_transient_update_plugins', create_function( '$a', "return null;" ) );
	wp_clear_scheduled_hook( 'wp_update_plugins' );
	
	
	//Diasable Core Updates
	# 2.8 to 3.0:
	remove_action( 'wp_version_check', 'wp_version_check' );
	remove_action( 'admin_init', '_maybe_update_core' );
	add_filter( 'pre_transient_update_core', create_function( '$a', "return null;" ) );
	wp_clear_scheduled_hook( 'wp_version_check' );
	
	# 3.0:
	add_filter( 'pre_site_transient_update_core', create_function( '$a', "return null;" ) );
	wp_clear_scheduled_hook( 'wp_version_check' );

}