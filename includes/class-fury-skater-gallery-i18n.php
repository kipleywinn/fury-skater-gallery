<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://rockstarrollerderby.com
 * @since      1.0.0
 *
 * @package    Fury_Skater_Gallery
 * @subpackage Fury_Skater_Gallery/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Fury_Skater_Gallery
 * @subpackage Fury_Skater_Gallery/includes
 * @author     Bi-Furious <hello@rockstarrollerderby.com>
 */
class Fury_Skater_Gallery_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'fury-skater-gallery',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
