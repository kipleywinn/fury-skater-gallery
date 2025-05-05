<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://rockstarrollerderby.com
 * @since      1.0.0
 *
 * @package    Fury_Skater_Gallery
 * @subpackage Fury_Skater_Gallery/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Fury_Skater_Gallery
 * @subpackage Fury_Skater_Gallery/includes
 * @author     Bi-Furious <hello@rockstarrollerderby.com>
 */
class Fury_Skater_Gallery_Deactivator {

	/**
	 * On deactivation, delete the gallery page.
	 *
	 * Get the "Skater Gallery" page id, check if it exists, and delete the page if it does.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		// Get saved page id
		$saved_page_id = get_option( 'fury_skater_gallery_display_page_id');

		// Check if the page exists
		if ( $saved_page_id ) {
			// delete the page
			wp_delete_post( $saved_page_id, true );

			// delete the id
			delete_option( 'fury_skater_gallery_display_page_id' );
		}

	}

}
