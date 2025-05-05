<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://rockstarrollerderby.com
 * @since      1.0.0
 *
 * @package    Fury_Skater_Gallery
 * @subpackage Fury_Skater_Gallery/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Fury_Skater_Gallery
 * @subpackage Fury_Skater_Gallery/admin
 * @author     Bi-Furious <hello@rockstarrollerderby.com>
 */
class Fury_Skater_Gallery_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		// Add the action hooks here, within the constructor
		add_action( 'edit_form_after_title', array( $this, 'fury_skater_gallery_output_team_logo_data' ) );

		add_action( 'add_meta_boxes', array( $this, 'add_team_logo_data_meta_box' ), 10, 2 );

		add_action( 'admin_init', array( $this, 'register_plugin_settings' ) );
		add_action( 'admin_menu', array( $this, 'reorder_fury_skater_gallery_submenu' ), 999 );





	}



/**
     * Adds the meta box to output team logo data.
     *
     * @param string $post_type The post type.
     * @param WP_Post $post The current post object.
     */
public function add_team_logo_data_meta_box( $post_type, $post ) {
	if ( 'skater' === $post_type ) {
		$this->fury_skater_gallery_output_team_logo_data( $post );
	}
}

    /**
     * Outputs team logo data as a data attribute on the 'edit skater' page.
     *
     * @param WP_Post $post The current post object.
     */
    public function fury_skater_gallery_output_team_logo_data( $post ) {
    	$terms = get_the_terms( $post->ID, 'team' );

    	if ( $terms && ! is_wp_error( $terms ) ) {
            $team = reset( $terms ); // Assuming only one team per skater (adjust if needed)
            $logo_id = get_term_meta( $team->term_id, '_team_logo_id', true );

            if ( $logo_id ) {
                $logo_url_array = wp_get_attachment_image_src( $logo_id, 'thumbnail' ); // Get thumbnail URL
                if ( $logo_url_array && isset( $logo_url_array[0] ) ) {
                	echo '<div id="current-skater-team" data-logo-url="' . esc_url( $logo_url_array[0] ) . '" style="display:none;"></div>';
                }
            }
        }
    }





	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles($hook) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Fury_Skater_Gallery_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Fury_Skater_Gallery_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

/*********** commenting out the styles for now because I don't want to load any, but keeping the code for reference 
 * 
		if ( 'settings_page_fury-skater-gallery' != $hook ) {
			return;
		}
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/fury-skater-gallery-admin.css', array(), $this->version, 'all' );

		**************/

	// 	if ( $hook !== 'toplevel_page_fury-skater-gallery' ) {
    //     return;
    // }

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/fury-skater-gallery-admin.css', array(), $this->version );

		wp_enqueue_style( 'wp-color-picker' );
	

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Fury_Skater_Gallery_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Fury_Skater_Gallery_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		/*********** commenting out the styles for now because I don't want to load any, but keeping the code for reference 
 
		if ( 'settings_page_fury-skater-gallery' != $hook ) {
			return;
		}

		wp_enqueue_script( $this->plugin_name, 
		plugin_dir_url( __FILE__ ) . 'js/fury-skater-gallery-admin.js',
		 array( 'jquery' ), 
		 $this->version, false );

		
		 *************/
		 wp_enqueue_script(
		 	$this->plugin_name,
		 	plugin_dir_url( __FILE__ ) . 'js/skater-bulk-quick-edit.js',
		 	array( 'jquery' ),
		 	$this->version,
		 	true
		 );
		 wp_localize_script(
		 	$this->plugin_name,
		 	'skater_bulk_edit',
		 	array(
		 		'nonce' => wp_create_nonce( 'skater_bulk_edit_nonce' )
		 	)
		 );

		
	 	
	 	wp_enqueue_script(
		'wp-color-picker'
	);

	wp_enqueue_script(
		'fury-color-picker-script', // A unique handle for your color picker script
		plugin_dir_url( __FILE__ ) . 'js/fury-color-picker.js',
		array( 'wp-color-picker', 'jquery' ), // Ensure wp-color-picker and jquery are dependencies
		$this->version,
		true
	);
		 



		}





	/**
	 * Enqueue scripts specifically for the Skater post type edit screen.
	 *
	 * @since 1.0.0
	 * @param string $hook The current admin page hook.
	 */
	public function enqueue_skater_edit_scripts( $hook ) {
		$screen = get_current_screen();

		if ( ( 'post.php' === $hook || 'post-new.php' === $hook ) && isset( $screen->post_type ) && 'skater' === $screen->post_type ) {
			wp_enqueue_script(
				'fury-skater-popup-generator',
				plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/skater-popup-generator.js',
				array( 'jquery' ),
				$this->version,
				true
			);
			wp_enqueue_script(
				'fury-skater-team-selection-js',
				plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/skater-team-selection-js.js',
				array( 'jquery' ),
				$this->version,
				true
			);
		}
	}
	public function skater_quick_edit_js( $hook ) {
		$screen = get_current_screen();
		if ($screen->post_type !== 'skater') return;
		wp_enqueue_script(
			'fury-skater-bulk-quick-edit',
			plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/skater-bulk-quick-edit.js',
			array( 'jquery' ),
			$this->version,
			true
		);

	}






	/**
	 * Highlight the top-level menu when viewing the Teams taxonomy.
	 *
	 * @param string $parent_file The parent file.
	 * @return string The parent file.
	 */
	public function highlight_teams_menu( $parent_file ) {
		global $current_screen;

		$taxonomy = $current_screen->taxonomy;

		if ( ('team' === $taxonomy) || ('moonlighting_team' === $taxonomy) ) {
			$parent_file = 'fury-skater-gallery';
		}

		return $parent_file;
	}

	/**
 * Highlight the correct submenu when viewing All Skaters or Add New Skater.
 */
	public function highlight_skater_menu( $submenu_file, $parent_file ) {
		global $current_screen;

		if (
		'edit-skater' === $current_screen->id || // All Skaters
		'skater' === $current_screen->id         // Add New Skater
	) {
			$parent_file  = 'fury-skater-gallery';
			$submenu_file = 'edit.php?post_type=skater';
		}

		return $submenu_file;
	}

	/**
	 * Highlight the specific submenu item for the Teams taxonomy.
	 *
	 * @param string $submenu_file The submenu file.
	 * @param string $parent_file  The parent file.
	 * @return string The submenu file.
	 */
	public function highlight_teams_submenu( $submenu_file, $parent_file ) {
		global $current_screen;

		if ( 'fury-skater-gallery' === $parent_file && isset( $current_screen->taxonomy ) && 'team' === $current_screen->taxonomy ) {
			$submenu_file = 'edit-tags.php?taxonomy=team&post_type=skater';
		}
		if ( 'fury-skater-gallery' === $parent_file && isset( $current_screen->taxonomy ) && 'moonlighting_team' === $current_screen->taxonomy ) {
			$submenu_file = 'edit-tags.php?taxonomy=moonlighting_team&post_type=skater';
		}

		return $submenu_file;
	}
	

	/**
 * Register the main menu page for the admin area.
 *
 * @since    1.0.0
 */
	public function register_main_menu_page() {
		add_menu_page(
		__( 'Fury Skater Gallery', 'fury-skater-gallery' ), // Page title
		__( 'Fury Skater Gallery', 'fury-skater-gallery' ), // Menu title
		'manage_options',                                  // Capability
		'fury-skater-gallery',                             // Menu slug (unique identifier)
		array( $this, 'display_main_page' ),               // Function to render the main page (optional)
		'dashicons-images-alt2',                           // Icon (optional - see WordPress Dashicons)
		25                                                // Menu position (optional - adjust as needed)
	);
	}

	/**
 * Register the settings page for the admin area.
 *
 * @since    1.0.0
 */
	public function register_settings_page() {
	// Create our settings page as a submenu page.
		
		// Custom landing page
		add_submenu_page(
	'fury-skater-gallery',                             // Parent slug (matches the top-level menu slug)
	__( 'Overview', 'fury-skater-gallery' ),           // Page title (shown in <title>)
	__( 'Overview', 'fury-skater-gallery' ),           // Menu title (shown in the submenu list)
	'manage_options',                                  // Capability
	'fury-skater-gallery',                             // Menu slug (same as the top-level one!)
	array( $this, 'display_main_page' )                // Function that renders the content
);

		add_submenu_page(
			'fury-skater-gallery',                                // Parent slug
			__( 'Teams', 'fury-skater-gallery' ),                // Page title
			__( 'Teams', 'fury-skater-gallery' ),                // Menu title
			'manage_categories',                                  // Capability
			'edit-tags.php?taxonomy=team&post_type=skater'      // Menu slug
		);

		if ( get_option( 'fury_skater_gallery_show_moonlighting_menu' ) ) {
			add_submenu_page(
				'fury-skater-gallery',                                // Parent slug
				__( 'Moonlighting Teams', 'fury-skater-gallery' ),                // Page title
				__( 'Moonlighting Teams', 'fury-skater-gallery' ),                // Menu title
				'manage_categories',                                  // Capability
				'edit-tags.php?taxonomy=moonlighting_team&post_type=skater'      // Menu slug
			);
		}

		add_submenu_page(
		'fury-skater-gallery',                             // parent slug
		__( 'Settings', 'fury-skater-gallery' ),      // page title
		__( 'Settings', 'fury-skater-gallery' ),      // menu title
		'manage_options',                        // capability
		'fury-skater-gallery-settings',                           // menu_slug
		array( $this, 'display_settings_page' )  // callable function
	);

		add_submenu_page(
		'fury-skater-gallery',                           // Parent slug
		__( 'Help', 'fury-skater-gallery' ),             // Page title
		__( 'Help', 'fury-skater-gallery' ),             // Menu title
		'manage_options',                                // Capability
		'fury-skater-gallery-help',                      // Menu slug
		array( $this, 'display_help_page' )              // Callback to render the page
	);

		// Hook into the 'parent_file' filter
		add_filter( 'parent_file', array( $this, 'highlight_teams_menu' ) );

		// Hook into the 'submenu_file' filter
		add_filter( 'submenu_file', array( $this, 'highlight_teams_submenu' ), 10, 2 );
		//add_filter( 'submenu_file', array( $this, 'highlight_moonlighting_teams_submenu' ), 10, 2 );

		add_filter( 'submenu_file', array( $this, 'highlight_skater_menu' ), 10, 2 );


	}

	public function reorder_fury_skater_gallery_submenu() {
		global $submenu;

		if ( isset( $submenu['fury-skater-gallery'] ) ) {
			$menu = $submenu['fury-skater-gallery'];

        // Find and remove the Overview item
			$overview = null;
			foreach ( $menu as $index => $item ) {
				if ( $item[2] === 'fury-skater-gallery' ) {
					$overview = $item;
					unset( $menu[$index] );
					break;
				}
			}

        // Prepend the Overview item if it exists
			if ( $overview ) {
				array_unshift( $menu, $overview );
			}

        // Reassign the modified menu
			$submenu['fury-skater-gallery'] = $menu;
		}
	}


/**
 * Display the settings page content for the page we have created.
 *
 * @since    1.0.0
 */
public function display_settings_page() {

	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/fury-skater-gallery-admin-settings-display.php';

}

/**
 * Display the settings page content for the page we have created.
 *
 * @since    1.0.0
 */
public function display_main_page() {

	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/fury-skater-gallery-admin-main-display.php';

}






public function register_plugin_settings() {
    // Register a setting group (a logical grouping of settings)
	register_setting(
        'fury_skater_gallery_settings_group', // Option group name
        'fury_skater_gallery_public_css',      // Option name (to store in wp_options table)
        array( $this, 'sanitize_public_css' )   // Sanitization callback
    );

    // Add a settings section (a visual grouping on the settings page)
	
	// Register the setting group (a logical grouping of settings)
    register_setting(
        'fury_skater_gallery_settings_group', // Option group name
        'fury_skater_gallery_modal_colors',   // New option name to store modal colors
        array( $this, 'sanitize_modal_colors' ) // Sanitization callback for modal colors
    );

    // Add a settings section (a visual grouping on the settings page)
    add_settings_section(
        'modal_colors_section',              // Section ID
        __( 'Modal Popup Colors', 'fury-skater-gallery' ), // Section title
        array( $this, 'modal_colors_section_info' ), // Callback to display section description
        'fury-skater-gallery-settings'       // The menu slug of your settings page
    );

    // Add settings fields for modal colors
    add_settings_field(
        'modal_overlay_color',                // Field ID
        __( 'Modal Overlay Color', 'fury-skater-gallery' ), // Field title
        array( $this, 'modal_color_field' ),    // Callback to display the input field
        'fury-skater-gallery-settings',        // The menu slug of your settings page
        'modal_colors_section',               // The section ID where this field belongs
        array( 'label_for' => 'modal_overlay_color' ) // Arguments passed to the callback function
    );

    add_settings_field(
        'modal_content_bg_color',             // Field ID
        __( 'Modal Content Background Color', 'fury-skater-gallery' ),
        array( $this, 'modal_color_field' ),
        'fury-skater-gallery-settings',
        'modal_colors_section',
        array( 'label_for' => 'modal_content_bg_color' )
    );

    add_settings_field(
        'modal_accent_color',                 // Field ID
        __( 'Modal Accent Color (Heading)', 'fury-skater-gallery' ),
        array( $this, 'modal_color_field' ),
        'fury-skater-gallery-settings',
        'modal_colors_section',
        array( 'label_for' => 'modal_accent_color' )
    );

    add_settings_field(
        'modal_text_color',                   // Field ID
        __( 'Modal Text Color', 'fury-skater-gallery' ),
        array( $this, 'modal_color_field' ),
        'fury-skater-gallery-settings',
        'modal_colors_section',
        array( 'label_for' => 'modal_text_color' )
    );

	add_settings_section(
        'public_css_section',                 // Section ID
        __( 'Public Display CSS', 'fury-skater-gallery' ), // Section title
        array( $this, 'public_css_section_info' ),      // Callback to display section description
        'fury-skater-gallery-settings'        // The menu slug of your settings page
    );

    // Add a settings field for the public CSS
	add_settings_field(
        'public_css',                         // Field ID
        __( 'Custom Public CSS', 'fury-skater-gallery' ), // Field title
        array( $this, 'public_css_field' ),          // Callback to display the input field
        'fury-skater-gallery-settings',        // The menu slug of your settings page
        'public_css_section'                  // The section ID where this field belongs
    );

	register_setting(
    'fury_skater_gallery_settings_group', // Same option group
    'fury_skater_gallery_show_moonlighting_menu', // Consistent option name
    array( 'sanitize_callback' => 'absint' ) // Optional sanitization for a checkbox
);

	add_settings_section(
		'fury_display_options',
		__( 'Admin Options', 'fury-skater-gallery' ),
		'__return_null',
		'fury-skater-gallery-settings'
	);

	add_settings_field(
		'fury_skater_gallery_show_moonlighting_menu',
		__( 'Show Moonlighting Team', 'fury-skater-gallery' ),
		function() {
			$value = get_option( 'fury_skater_gallery_show_moonlighting_menu' );
			echo '<input type="checkbox" id="fury_skater_gallery_show_moonlighting_menu" name="fury_skater_gallery_show_moonlighting_menu" value="1" ' . checked( 1, $value, false ) . '> <label for="fury_skater_gallery_show_moonlighting_menu">';
			_e( 'Show the Moonlighting Team taxonomy in the menu.', 'fury-skater-gallery' );
			echo '</label><p class="description">';
			_e( 'Enable this to make the Moonlighting Team taxonomy visible in the admin menu. Use this only for troubleshooting.', 'fury-skater-gallery' );
			echo '</p>';
		},
		'fury-skater-gallery-settings',
		'fury_display_options'
	);

}

public function sanitize_public_css( $input ) {
    // Basic sanitization: strip potentially harmful tags and attributes
	return wp_kses_post( $input );
    // For more advanced CSS validation, you might use a dedicated CSS parser/validator
}

public function public_css_section_info() {
	echo '<p>' . __( 'Customize the appearance of the skater gallery on your website.', 'fury-skater-gallery' ) . '</p>';
}

public function public_css_field() {
	$options = get_option( 'fury_skater_gallery_public_css' );
	$css = isset( $options ) ? $options : '';
	?>
	<textarea id="public_css" name="fury_skater_gallery_public_css" rows="10" cols="80"><?php echo esc_textarea( $css ); ?></textarea>
	<p class="description"><?php _e( 'Enter your custom CSS here. This will be applied to the public-facing skater gallery.', 'fury-skater-gallery' ); ?></p>
	<?php
}

public function sanitize_modal_colors( $input ) {
    // Sanitize the input to ensure valid hex color values
    $output = array();
    foreach ( $input as $key => $value ) {
        $output[ $key ] = sanitize_hex_color( $value );
    }
    return $output;
}

public function modal_colors_section_info() {
    echo '<p>' . __( 'Customize the colors of the modal popup on the skater gallery.', 'fury-skater-gallery' ) . '</p>';
}

public function modal_color_field( $args ) {
    // Get current color options
    $options = get_option( 'fury_skater_gallery_modal_colors' );
    $value = isset( $options[ $args['label_for'] ] ) ? esc_attr( $options[ $args['label_for'] ] ) : '';
    
    // Display color input field
    echo '<input type="text" id="' . esc_attr( $args['label_for'] ) . '" name="fury_skater_gallery_modal_colors[' . esc_attr( $args['label_for'] ) . ']" value="' . $value . '" class="regular-text fury-color-picker" />';
}




public function display_help_page() {
	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/fury-skater-gallery-admin-help-display.php';
}







}

