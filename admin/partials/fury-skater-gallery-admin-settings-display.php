<?php
/**
 * Provide a public-facing view for the admin settings page of the plugin.
 *
 * @link       https://rockstarrollerderby.com
 * @since      1.0.0
 *
 * @package    Fury_Skater_Gallery
 * @subpackage Fury_Skater_Gallery/admin/partials
 */
?>

<div class="wrap">
    <h2><?php esc_html_e( 'Fury Skater Gallery Settings', 'fury-skater-gallery' ); ?></h2>
    <form method="post" action="options.php">
        <?php
            settings_fields( 'fury_skater_gallery_settings_group' ); // Output settings fields for the group
            do_settings_sections( 'fury-skater-gallery-settings' ); // Output settings sections for the page slug
            submit_button();
        ?>
    </form>
</div>