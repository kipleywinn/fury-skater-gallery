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
   * Registers the fury-skater-gallery-display shortcode.
   * This should be called during the 'init' action.
   */
  public function register_shortcode() {
    add_shortcode( 'fury-skater-gallery-display', array( $this, 'display_shortcode_callback' ) );
  }

  /**
   * Helper function to convert team color hex codes to rgb (for use with opacity in CSS file)
  */
  private function hexToRgbCssVariable($hex) {
    $hex = preg_replace("/[^0-9A-Fa-f]/", '', $hex);
    if (strlen($hex) != 6) {
        return '0,0,0'; // Or some other default RGB value
      }
      $r = hexdec(substr($hex, 0, 2));
      $g = hexdec(substr($hex, 2, 2));
      $b = hexdec(substr($hex, 4, 2));
      return $r . ' ' . $g . ' ' . $b;
    }

  /**
   * Callback function for the fury-skater-gallery-display shortcode.
   *
   * This function will fetch and display the skater gallery.
   *
   * @param array $atts Shortcode attributes.
   * @return string HTML output of the skater gallery.
   */
  public function display_shortcode_callback( $atts ) {

    $gallery_id = uniqid( 'furyGallery_' );

    
    $atts = shortcode_atts( array(
      'team' => '',
      'type' => '',
      'roster' => 'false',
), $atts, 'fury-skater-gallery-display' );

    $args = array(
      'post_type'      => 'skater',
      'posts_per_page' => -1,
      'orderby'        => 'title',
      'order'          => 'ASC',
    );

    $moonlighting_args = array(
          'post_type'      => 'skater',
          'posts_per_page' => -1,
          'orderby'        => 'title',
          'order'          => 'ASC',
        );

// Handle type filter (crew, skaters, or all)
    $tax_query = array();
    $meta_query = array();
    $moonlighting_tax_query = array();
    $moonlighting_meta_query = array();

    if ( $atts['type'] === 'skaters' ) {
      $tax_query[] = array(
        'taxonomy' => 'team',
        'field'    => 'slug',
        'terms'    => array( 'crew' ),
        'operator' => 'NOT IN',
      );
    } elseif ( $atts['type'] === 'crew' ) {
      $tax_query[] = array(
        'taxonomy' => 'team',
        'field'    => 'slug',
        'terms'    => array( 'crew' ),
      );
    }

// Handle team filter (overrides 'type' filter if both are set)
    if ( ! empty( $atts['team'] ) ) {
  $tax_query = array( // override previous
    array(
      'taxonomy' => 'team',
      'field'    => 'slug',
      'terms'    => sanitize_text_field( $atts['team'] ),
    ),
  );
}

// Handle roster filter
  // default false, user set to "true" means only show bouting or moonlighting skaters for team set in atts "team"
    if ( $atts['roster'] === 'true' ) {

      $meta_query = array(
          array(
            'key' => 'roster_status_group_bouting_status',
            'value' => 'yes',
            'compare' => '='
          ),
        );

      if ( ! empty( $atts['team'] ) ) {
      $tax_query = array(
        array(
          'taxonomy' => 'team',
          'field'    => 'slug',
          'terms'    => sanitize_text_field( $atts['team'] ),
        ),
      );

      $moonlighting_tax_query = array(
        array(
          'taxonomy' => 'moonlighting_team',
          'field'    => 'slug',
          'terms'    => sanitize_text_field( $atts['team'] ),
        ),
      );
      $moonlighting_meta_query = array(
          array(
            'key' => 'roster_status_group_moonlighting_status',
            'value' => 'yes',
            'compare' => '='
          ),
        );


      } else {
        $tax_query = array();
      }
    
  }



if ( ! empty( $tax_query ) ) {
  $args['tax_query'] = $tax_query;
}
if ( ! empty( $meta_query ) ) {
  $args['meta_query'] = $meta_query;
}

if ( ! empty( $moonlighting_tax_query ) ) {
  $moonlighting_args['tax_query'] = $moonlighting_tax_query;
}
if ( ! empty( $moonlighting_meta_query ) ) {
  $moonlighting_args['meta_query'] = $moonlighting_meta_query;
}


$displayed_teams = array();
$skaters_query = new WP_Query( $args );



if ( (! empty( $moonlighting_meta_query)) && (! empty($moonlighting_tax_query)) ) {
  $moonlighting_skaters_query = new WP_Query( $moonlighting_args );
}


if ( $skaters_query->have_posts() ) {
  while ( $skaters_query->have_posts() ) {
    $skaters_query->the_post();
    $skater_id    = get_the_ID();
    $teams        = wp_get_post_terms( $skater_id, 'team' );
    if ( $teams && ! is_wp_error( $teams ) ) {
      foreach ( $teams as $team ) {
        $displayed_teams[ $team->slug ] = $team->name;
      }
    }
        // No need to build the skater HTML here yet, we'll do it after the dropdown
  }
  wp_reset_postdata();
}





$output = '<div class="fury-skater-gallery" id="' . esc_attr( $gallery_id ) . '">';

$output .= '<div class="skaterGalleryInner">';

if ( empty( $atts['team'] ) && ( empty( $atts['type'] ) || $atts['type'] === 'skaters' ) ) {
  $output .= '<div id="filterMenu">';
  $output .= '<div id="teamSelectDiv">';
  $output .= '<label for="teams">' . esc_html__( 'Filter by team:', 'fury-skater-gallery' ) . '</label>';

  // Alphabetize teams by name
  asort( $displayed_teams );

  $output .= '<select name="teams" class="teamSelect">';
  $output .= '<option value="all">' . esc_html__( 'See all', 'fury-skater-gallery' ) . '</option>';
  foreach ( $displayed_teams as $slug => $name ) {
    $output .= '<option value="' . esc_attr( $slug ) . '">' . esc_html( $name ) . '</option>';
  }
  $output .= '</select>';
  $output .= '</div>';
  $output .= '</div>';
}

$output .= '<div class="photoThumbnails">';



if ( $skaters_query->have_posts() ) {
  $skater_key_counter = 0;
  while ( $skaters_query->have_posts() ) {
    $skaters_query->the_post();
    $skater_id    = get_the_ID();
    $headshot_url = get_the_post_thumbnail_url( $skater_id, 'medium' );
    $skater_number = get_post_meta( $skater_id, '_skater_number', true );
    $teams        = wp_get_post_terms( $skater_id, 'team' );
    $bio          = get_field( 'skater_popup_html', $skater_id );
    $skater_name  = get_the_title();
    $skater_key   = 'skater-' . $skater_id . '-' . $skater_key_counter++;

    $team_slugs = array();
    $team_names = array();
    $css_vars = '';
    if ( ! empty( $teams ) ) {
      foreach ( $teams as $team ) {
        $team_slugs[] = $team->slug;
        $team_color_hex = get_term_meta( $team->term_id, '_team_color', true );
        if ( $team_color_hex ) {
          $css_vars .= '--team-' . esc_attr( $team->slug ) . '-color: ' . esc_attr( $team_color_hex ) . '; ';
          $css_vars .= '--team-' . esc_attr( $team->slug ) . '-color-rgb: ' . $this->hexToRgbCssVariable( $team_color_hex ) . '; '; // Call it as a method
        }
      }
    }
$team_slug_class = ! empty( $team_slugs ) ? esc_attr( implode( ' ', $team_slugs ) ) : '';
$style_attr = ! empty( $css_vars ) ? 'style="' . trim( $css_vars ) . '"' : '';

$output .= '<div id="' . esc_attr( $skater_key ) . '" class="photoCard ' . $team_slug_class . '" ' . $style_attr . ' data-skater-key="' . esc_attr( $skater_key ) . '">';
$output .= '<div class="photo-container">';
$output .= '<div class="photo">';
if ( $headshot_url ) {
  $output .= '<img class="theThumbnail" src="' . esc_url( $headshot_url ) . '" alt="' . esc_attr( $skater_name ) . '" />';
}
$output .= '<div class="imageOverlay">';
$output .= '<div class="imageOverlayText">';
$output .= '<img src="https://rockstarrollerderby.com/wp-content/uploads/2025/04/eye-icon-white.png" />';
$output .= '</div>';
        $output .= '</div>'; // Close imageOverlay
        $output .= '</div>'; // Close photo
        $output .= '</div>'; // Close photo-container
        $output .= '<div class="title">' . esc_html( $skater_name );
        if ( ! empty( $skater_number ) ) {
          $output .= ' #' . esc_html( $skater_number );
        }
        $output .= '</div>';
        $output .= '</div>'; // Close photoCard
        $output .= '<div class="skater-bio-content hidden" data-skater-key="' . esc_attr( $skater_key ) . '">' . $bio . '</div>';

      }
      wp_reset_postdata();

            if ( (! empty( $moonlighting_meta_query)) && (! empty($moonlighting_tax_query)) ) { 
              if ( $moonlighting_skaters_query->have_posts() ) {
                    while ( $moonlighting_skaters_query->have_posts() ) {
                      $moonlighting_skaters_query->the_post();
                      $skater_id    = get_the_ID();
                      $headshot_url = get_the_post_thumbnail_url( $skater_id, 'medium' );
                      $skater_number = get_post_meta( $skater_id, '_skater_number', true );
                      $teams        = wp_get_post_terms( $skater_id, 'team' );
                      $bio          = get_field( 'skater_popup_html', $skater_id );
                      $skater_name  = get_the_title();
                      $skater_key   = 'skater-' . $skater_id . '-' . $skater_key_counter++;
            
                      $team_slugs = array();
                      $css_vars = '';
                      if ( ! empty( $teams ) ) {
                        foreach ( $teams as $team ) {
                          $team_slugs[] = $team->slug;
                          $team_color_hex = get_term_meta( $team->term_id, '_team_color', true );
                          if ( $team_color_hex ) {
                            $css_vars .= '--team-' . esc_attr( $team->slug ) . '-color: ' . esc_attr( $team_color_hex ) . '; ';
                            $css_vars .= '--team-' . esc_attr( $team->slug ) . '-color-rgb: ' . $this->hexToRgbCssVariable( $team_color_hex ) . '; ';
                          }
                        }
                      }
            
                      $team_slug_class = ! empty( $team_slugs ) ? esc_attr( implode( ' ', $team_slugs ) ) : '';
                      $style_attr = ! empty( $css_vars ) ? 'style="' . trim( $css_vars ) . '"' : '';
            
                      $output .= '<div id="' . esc_attr( $skater_key ) . '" class="photoCard ' . $team_slug_class . ' moonlighting" ' . $style_attr . ' data-skater-key="' . esc_attr( $skater_key ) . '">';
                      $output .= '<div class="photo-container">';
                      $output .= '<div class="photo">';
                      if ( $headshot_url ) {
                        $output .= '<img class="theThumbnail" src="' . esc_url( $headshot_url ) . '" alt="' . esc_attr( $skater_name ) . '" />';
                      }
                      $output .= '<div class="imageOverlay">';
                      $output .= '<div class="imageOverlayText">';
                      $output .= '<img src="https://rockstarrollerderby.com/wp-content/uploads/2025/04/eye-icon-white.png" />';
                      $output .= '</div></div></div></div>';
                      $output .= '<div class="title">' . esc_html( $skater_name );
                      if ( ! empty( $skater_number ) ) {
                        $output .= ' #' . esc_html( $skater_number );
                      }
                      $output .= '</div>';
                      $output .= '</div>'; // Close photoCard
                      $output .= '<div class="skater-bio-content hidden" data-skater-key="' . esc_attr( $skater_key ) . '">' . $bio . '</div>';
                    }
                    wp_reset_postdata();
                  }}





    } else {
      $output .= '<p>' . esc_html__( 'No skaters found.', 'fury-skater-gallery' ) . '</p>';
    }

    $output .= '</div>';
    $output .= '</div>'; // Close #skaterGallery
    $output .= '</div>'; // Close .fury-skater-gallery

    // Add single modal wrapper at the end
    $output .= '
    <div id="skater-bio-modal" class="skaterModal hidden">
      <div class="skaterModalOverlay"></div>
      <div class="skaterModalContent">
        <button class="skaterModalClose" aria-label="Close modal">×</button>
        <div id="skater-bio-modal-inner"></div>
      </div>
    </div>';

return $output;

  }

}
?>