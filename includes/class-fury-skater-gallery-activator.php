<?php

/**
 * Fired during plugin activation
 *
 * @link       https://rockstarrollerderby.com
 * @since      1.0.0
 *
 * @package    Fury_Skater_Gallery
 * @subpackage Fury_Skater_Gallery/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Fury_Skater_Gallery
 * @subpackage Fury_Skater_Gallery/includes
 * @author     Bi-Furious <hello@rockstarrollerderby.com>
 */
class Fury_Skater_Gallery_Activator {

	/**
	 * On activation, create the gallery page and remember it.
	 *
	 * Create a page named "Skater Gallery", add a shortcode that will show the gallery anywhere, and remember the page id in the database.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		// Saved Page Arguments
		$saved_page_args = array(
			'post_title'   => __( 'Skater Gallery', 'fury-skater-gallery' ),
			'post_content' => '[fury-skater-gallery]',
			'post_status'  => 'published',
			'post_type'    => 'page'
		);
		// Insert the page and get its id.
		$saved_page_id = wp_insert_post( $saved_page_args );
		// Save page id to the database.
		add_option( 'fury_skater_gallery_display_page_id', $saved_page_id );


	}

}
