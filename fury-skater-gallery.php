<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link           https://rockstarrollerderby.com
 * @since          1.0.0
 * @package        Fury_Skater_Gallery
 *
 * @wordpress-plugin
 * Plugin Name:      Fury Skater Gallery
 * Plugin URI:       https://rockstarrollerderby.com
 * Description:      Allows admin to manage skater data and display it in a gallery.
 * Version:          1.1.0
 * Author:           Bi-Furious
 * Author URI:       https://rockstarrollerderby.com/
 * License:          GPL-2.0+
 * License URI:      http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:      fury-skater-gallery
 * Domain Path:      /languages
 * Requires Plugins: advanced-custom-fields
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
if ( ! defined( 'FURY_SKATER_GALLERY_VERSION' ) ) {
	define( 'FURY_SKATER_GALLERY_VERSION', '1.1.0' );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-fury-skater-gallery-activator.php
 */
function activate_fury_skater_gallery() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fury-skater-gallery-activator.php';
	Fury_Skater_Gallery_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-fury-skater-gallery-deactivator.php
 */
function deactivate_fury_skater_gallery() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fury-skater-gallery-deactivator.php';
	Fury_Skater_Gallery_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_fury_skater_gallery' );
register_deactivation_hook( __FILE__, 'deactivate_fury_skater_gallery' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-fury-skater-gallery.php';

/**
 * Include the file for registering custom post types and taxonomies.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-fury-skater-gallery-post-types.php';

/**
 * Include the file that registers ACF fields programmatically.
 */
if ( function_exists( 'acf_add_local_field_group' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fury-skater-gallery-acf-fields.php';
}


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_fury_skater_gallery() {
	$plugin = new Fury_Skater_Gallery();
	$plugin->run();

	// Instantiate the post types class and pass the version
	if ( class_exists( 'Fury_Skater_Gallery_Post_Types' ) ) {
		$fsg_post_types = new Fury_Skater_Gallery_Post_Types( FURY_SKATER_GALLERY_VERSION );
	}
}
run_fury_skater_gallery();

