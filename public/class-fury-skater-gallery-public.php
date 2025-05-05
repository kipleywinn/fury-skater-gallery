<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link    https://rockstarrollerderby.com
 * @since   1.0.0
 *
 * @package Fury_Skater_Gallery
 * @subpackage Fury_Skater_Gallery/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and hooks for how to
 * enqueue the public-facing stylesheet and JavaScript, and registers the shortcode.
 *
 * @package Fury_Skater_Gallery
 * @subpackage Fury_Skater_Gallery/public
 * @author    Bi-Furious <hello@rockstarrollerderby.com>
 */
class Fury_Skater_Gallery_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since   1.0.0
	 * @access  private
	 * @var     string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since   1.0.0
	 * @access  private
	 * @var     string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   1.0.0
	 * @param     string    $plugin_name      The name of the plugin.
	 * @param     string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		add_action( 'wp_head', array( $this, 'apply_modal_colors' ) );

	}

	/**
     * Apply the modal colors dynamically using CSS variables.
     */
    public function apply_modal_colors() {
        // Get saved modal colors
        $modal_colors = get_option( 'fury_skater_gallery_modal_colors' );

        // Set default colors if not set
        $overlay_color = isset( $modal_colors['modal_overlay_color'] ) ? esc_attr( $modal_colors['modal_overlay_color'] ) : '#000000';
        $content_bg_color = isset( $modal_colors['modal_content_bg_color'] ) ? esc_attr( $modal_colors['modal_content_bg_color'] ) : '#000000';
        $accent_color = isset( $modal_colors['modal_accent_color'] ) ? esc_attr( $modal_colors['modal_accent_color'] ) : '#eb2828';
        $text_color = isset( $modal_colors['modal_text_color'] ) ? esc_attr( $modal_colors['modal_text_color'] ) : '#ffffff';
      
        // Convert overlay hex to RGB for CSS variable
				$overlay_color_rgb = $this->hexToRgbCssVariable( $overlay_color );


        // Output CSS variables in the root selector
        echo "
        <style>
            :root {
                --modal-overlay-color: {$overlay_color};
                --modal-overlay-color-rgb: {$overlay_color_rgb};
                --modal-content-bg-color: {$content_bg_color};
                --modal-accent-color: {$accent_color};
                --modal-text-color: {$text_color};
            }
        </style>
        ";
    }

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since   1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/fury-skater-gallery-public.css', array(), $this->version, 'all' );

		$custom_css = get_option( 'fury_skater_gallery_public_css' );
		if ( ! empty( $custom_css ) ) {
			wp_add_inline_style( $this->plugin_name, $custom_css );
		}

	}



	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since   1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/fury-skater-gallery-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Registers the fury-skater-gallery shortcode.
	 * This should be called during the 'init' action.
	 */
	public function register_shortcode() {
		add_shortcode( 'fury-skater-gallery', array( $this, 'display_shortcode_callback' ) );
	}

	/**
	 * Helper function to convert team color hex codes to rgb (for use with opacity in CSS file)
	*/
	// private function hexToRgbCssVariable($hex) {
	// 	$hex = preg_replace("/[^0-9A-Fa-f]/", '', $hex);
	// 	if (strlen($hex) != 6) {
  //       return '0,0,0'; // Or some other default RGB value
  //     }
  //     $r = hexdec(substr($hex, 0, 2));
  //     $g = hexdec(substr($hex, 2, 2));
  //     $b = hexdec(substr($hex, 4, 2));
  //     return $r . ' ' . $g . ' ' . $b;
  //   }

	/**
	 * Callback function for the fury-skater-gallery shortcode.
	 *
	 * This function will fetch and display the skater gallery.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output of the skater gallery.
	 */


	public function display_shortcode_callback( $atts ) {
		$gallery_id = uniqid( 'furyGallery_' );
		$atts = shortcode_atts( [
			'team'   => '',
			'type'   => '',
			'roster' => 'false',
		], $atts, 'fury-skater-gallery' );

		$main_args         = $this->build_query_args( $atts, false );
		$moonlighting_args = $this->build_query_args( $atts, true );

		// var_dump($main_args);
		// var_dump($moonlighting_args);

		$displayed_teams = $this->get_displayed_teams( $main_args );
		$output          = $this->open_gallery_wrapper( $gallery_id );
		$output         .= $this->render_filter_menu( $atts, $displayed_teams );
		$output         .= '<div class="photoThumbnails">';

		$output .= $this->render_skaters( $main_args );

		if ( ! empty( $moonlighting_args ) ) {
			$output .= $this->render_skaters( $moonlighting_args, true );
		}

		$output .= '</div></div></div>'; // close photoThumbnails, skaterGalleryInner, fury-skater-gallery
		$output .= $this->render_modal_wrapper();
		return $output;
	}

	private function build_query_args( $atts, $moonlighting = false ) {
    $args = [
        'post_type'      => 'skater',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    ];

    // Default to showing all published skaters if no attributes are provided
    if ( ! $moonlighting && empty($atts['team']) && empty($atts['type']) ) {
        // No attributes, just return the query for all skaters
        $args['post_status'] = 'publish'; // Ensure only published skaters are shown
        return $args; // Return query to fetch all published skaters
    }

    $tax_query  = [];
    $meta_query = [];

    if ( $atts['roster'] === 'true' ) {
        if ( $moonlighting ) {
            // Only build this query when a team is specified
            if ( ! empty( $atts['team'] ) ) {
                $meta_query[] = [
                    'key'     => 'roster_status_group_moonlighting_status',
                    'value'   => 'yes',
                    'compare' => '=',
                ];
                $tax_query[] = [
                    'taxonomy' => 'moonlighting_team',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field( $atts['team'] ),
                ];
            }
        } else {
            // Always filter by bouting_status if roster=true
            $meta_query[] = [
                'key'     => 'roster_status_group_bouting_status',
                'value'   => 'yes',
                'compare' => '=',
            ];
            // Add team taxonomy only if team is provided
            if ( ! empty( $atts['team'] ) ) {
                $tax_query[] = [
                    'taxonomy' => 'team',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field( $atts['team'] ),
                ];
            }
        }
    } elseif ( ! $moonlighting ) {
        // Handle full lists (not rosters)
        if ( ! empty( $atts['team'] ) ) {
            $tax_query[] = [
                'taxonomy' => 'team',
                'field'    => 'slug',
                'terms'    => sanitize_text_field( $atts['team'] ),
            ];
        } elseif ( $atts['type'] === 'skaters' ) {
            $tax_query[] = [
                'taxonomy' => 'team',
                'field'    => 'slug',
                'terms'    => [ 'crew' ],
                'operator' => 'NOT IN',
            ];
        } elseif ( $atts['type'] === 'crew' ) {
            $tax_query[] = [
                'taxonomy' => 'team',
                'field'    => 'slug',
                'terms'    => [ 'crew' ],
            ];
        }
    }

    if ( ! empty( $tax_query ) ) {
        $args['tax_query'] = $tax_query;
    }

    if ( ! empty( $meta_query ) ) {
        $args['meta_query'] = $meta_query;
    }

    return ( $meta_query || $tax_query ) ? $args : [];
}


	private function get_displayed_teams( $args ) {
		$teams = [];
		$query = new WP_Query( $args );
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$skater_teams = wp_get_post_terms( get_the_ID(), 'team' );
				foreach ( $skater_teams as $team ) {
					$teams[ $team->slug ] = $team->name;
				}
			}
			wp_reset_postdata();
		}
		return $teams;
	}

	private function render_filter_menu( $atts, $teams ) {
		if ( ! empty( $atts['team'] ) || ( ! empty( $atts['type'] ) && $atts['type'] !== 'skaters' ) ) {
			return '';
		}
		asort( $teams );
		$output = '<div id="filterMenu"><div id="teamSelectDiv">';
		$output .= '<label for="teams">' . esc_html__( 'Filter by team:', 'fury-skater-gallery' ) . '</label>';
		$output .= '<select name="teams" class="teamSelect">';
		$output .= '<option value="all">' . esc_html__( 'See all', 'fury-skater-gallery' ) . '</option>';
		foreach ( $teams as $slug => $name ) {
			$output .= '<option value="' . esc_attr( $slug ) . '">' . esc_html( $name ) . '</option>';
		}
		$output .= '</select></div></div>';
		return $output;
	}

	private function render_skaters( $args, $is_moonlighting = false ) {
		$output = '';
		$query  = new WP_Query( $args );
		$counter = 0;

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$id       = get_the_ID();
				$name     = get_the_title();
				$number   = get_post_meta( $id, '_skater_number', true );
				$bio      = get_field( 'skater_popup_html', $id );
				$image    = get_the_post_thumbnail_url( $id, 'medium' );
				$teams    = wp_get_post_terms( $id, 'team' );
				$key      = 'skater-' . $id . '-' . $counter++;

				$team_slugs = [];
				$css_border_vars   = '';
				$css_overlay_vars = '';
				foreach ( $teams as $team ) {
					$team_slugs[] = $team->slug;
					$color        = get_term_meta( $team->term_id, '_team_color', true );
					if ( $color ) {
						// $css_border_vars .= '--team-' . esc_attr( $team->slug ) . '-color: ' . esc_attr( $color ) . '; ';
						// $css_border_vars .= '--team-' . esc_attr( $team->slug ) . '-color-rgb: ' . $this->hexToRgbCssVariable( $color ) . '; ';
						$css_border_vars .= 'border: 4px solid ' . esc_attr( $color) . '; ';
						$css_overlay_vars .= 'background-color: rgba(' . $this->hexToRgbCssVariable($color) . ' / 0.6);';
					}
				}

				$class_attr = esc_attr( implode( ' ', $team_slugs ) . ( $is_moonlighting ? ' moonlighting' : '' ) );
				$style_attr_border = ! empty( $css_border_vars ) ? 'style="' . trim( $css_border_vars ) . '"' : '';
				$style_attr_overlay = ! empty( $css_overlay_vars ) ? 'style="' . trim( $css_overlay_vars ) . '"' : '';

				$output .= '<div id="' . esc_attr( $key ) . '" class="photoCard ' . $class_attr . '" ' . $style_attr_border . ' data-skater-key="' . esc_attr( $key ) . '">';
				$output .= '<div class="photo-container"><div class="photo">';
				if ( $image ) {
					$output .= '<img class="theThumbnail" src="' . esc_url( $image ) . '" alt="' . esc_attr( $name ) . '" />';
				}
				$output .= '<div class="imageOverlay" ' . $style_attr_overlay . '><div class="imageOverlayText">';
				$output .= '<img src="https://rockstarrollerderby.com/wp-content/uploads/2025/04/eye-icon-white.png" /></div></div></div></div>';
				$output .= '<div class="title">' . esc_html( $name );
				if ( $number ) {
					$output .= ' #' . esc_html( $number );
				}
				$output .= '</div></div>';
				$output .= '<div class="skater-bio-content hidden" data-skater-key="' . esc_attr( $key ) . '">' . $bio . '</div>';
			}
			wp_reset_postdata();
		} elseif ( ! $is_moonlighting ) {
		// Only show message if it's not a moonlighting query
		$output .= '<p>' . esc_html__( 'No skaters found.', 'fury-skater-gallery' ) . '</p>';
	}

		return $output;
	}

	private function render_modal_wrapper() {
		return '<div id="skater-bio-modal" class="skaterModal hidden">'
			. '<div class="skaterModalOverlay"></div>'
			. '<div class="skaterModalContent">'
			. '<button class="skaterModalClose" aria-label="Close modal">×</button>'
			. '<div id="skater-bio-modal-inner"></div>'
			. '</div></div>';
	}

	private function open_gallery_wrapper( $id ) {
		return '<div class="fury-skater-gallery" id="' . esc_attr( $id ) . '"><div class="skaterGalleryInner">';
	}

	private function hexToRgbCssVariable( $hex ) {
		$hex = ltrim( $hex, '#' );
		if ( strlen( $hex ) === 3 ) {
			$hex = preg_replace( '/(.)/', '$1$1', $hex );
		}
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );
		return "$r $g $b";
	}









}


?>