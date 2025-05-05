<?php
/**
 * Registers custom post types and taxonomies for the Fury Skater Gallery plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
    die;
}

if ( ! class_exists( 'Fury_Skater_Gallery_Post_Types' ) ) {
    class Fury_Skater_Gallery_Post_Types {

        protected $version;

        public function __construct( $version ) {
            $this->version = $version;
            add_action( 'init', array( $this, 'register_skater_cpt' ) );
            add_action( 'init', array( $this, 'register_team_taxonomy' ), 0 );
            add_action( 'init', array( $this, 'register_moonlighting_team_taxonomy' ), 0 );
            add_action( 'add_meta_boxes_skater', array( $this, 'add_skater_meta_boxes' ), 1 );
            add_action( 'save_post_skater', array( $this, 'save_skater_meta' ) );
            add_action( 'created_term', array( $this, 'sync_team_terms_to_moonlighting_team' ), 10, 3 );
            add_filter( 'pre_delete_term', array( $this, 'sync_moonlighting_team_before_team_deletion' ), 10, 2 );



            // Team taxonomy custom fields
            add_action( 'team_edit_form_fields', array( $this, 'add_team_color_field' ), 10, 1 );
            add_action( 'team_add_form_fields', array( $this, 'add_new_team_color_field' ), 10, 1 );
            add_action( 'edited_term', array( $this, 'save_team_color' ), 10, 3 );
            add_action( 'create_term', array( $this, 'save_team_color' ), 10, 3 );

            add_action( 'team_edit_form_fields', array( $this, 'add_team_logo_field' ), 10, 1 );
            add_action( 'team_add_form_fields', array( $this, 'add_new_team_logo_field' ), 10, 1 );
            add_action( 'edited_term', array( $this, 'save_team_logo' ), 10, 3 );
            add_action( 'create_term', array( $this, 'save_team_logo' ), 10, 3 );

            add_action( 'manage_team_custom_column', array( $this, 'manage_team_logo_column' ), 10, 3 );

            // Enqueue media uploader scripts for the Team taxonomy
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_team_logo_scripts' ) );

            // Enqueue color picker scripts in the admin
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_color_picker' ) );

            // Custom columns for the 'team' taxonomy
            add_filter( 'manage_edit-team_columns', array( $this, 'add_team_color_column' ) );
            add_action( 'manage_team_custom_column', array( $this, 'manage_team_color_column' ), 10, 3 );

            // Custom columns for the Skater post type
            add_filter( 'manage_skater_posts_columns', array( $this, 'add_skater_number_column' ) );
            add_action( 'manage_skater_posts_custom_column', array( $this, 'manage_skater_number_column' ), 10, 2 );
            // Make custom columns sortable
            add_filter( 'manage_edit-skater_sortable_columns', array( $this, 'make_skater_number_sortable' ) );
            add_action( 'pre_get_posts', array( $this, 'skater_posts_orderby_custom' ), 10, 3 );
            add_action( 'restrict_manage_posts', array( $this, 'add_team_filter_dropdown' ), 10, 3 );
            add_action( 'pre_get_posts', array( $this, 'filter_posts_by_team' ), 10, 3 );



            add_action( 'quick_edit_custom_box', array( $this, 'add_skater_quick_edit_fields' ), 10, 2 );
            add_action( 'bulk_edit_custom_box', array( $this, 'add_skater_quick_edit_fields' ), 10, 2 );
            add_action( 'save_post', array ( $this, 'skater_status_quick_edit_save' ), 10, 2 );
            add_action( 'wp_ajax_save_skater_bulk_edit', array( $this, 'save_skater_bulk_edit' ), 10, 2 );
            



        }











/**
         * Add custom fields to the Team taxonomy edit form.
         *
         * @param WP_Term $term The term being edited.
         */
public function add_team_logo_field( $term ) {
    $logo_id = get_term_meta( $term->term_id, '_team_logo_id', true );
    $logo_url = $logo_id ? wp_get_attachment_image_src( $logo_id, 'thumbnail' )[0] : '';
    ?>
    <tr class="form-field term-logo-wrap">
        <th scope="row"><label for="team_logo"><?php _e( 'Team Logo', 'fury-skater-gallery' ); ?></label></th>
        <td>
            <div class="team-logo-field">
                <input type="hidden" id="team_logo_id" name="team_logo_id" value="<?php echo esc_attr( $logo_id ); ?>">
                <?php if ( $logo_url ) : ?>
                    <img src="<?php echo esc_url( $logo_url ); ?>" style="max-width: 150px; height: auto; display: block; margin-bottom: 10px;">
                <?php endif; ?>
                <p>
                    <button type="button" class="upload_image_button button button-secondary"><?php _e( 'Upload/Choose Image', 'fury-skater-gallery' ); ?></button>
                    <button type="button" class="remove_image_button button button-secondary" style="<?php echo $logo_url ? '' : 'display:none;'; ?>"><?php _e( 'Remove Image', 'fury-skater-gallery' ); ?></button>
                </p>
            </div>
        </td>
    </tr>
    <?php
}

        /**
         * Add custom fields to the Team taxonomy add new form.
         *
         * @param WP_Taxonomy $taxonomy The taxonomy being created.
         */
        public function add_new_team_logo_field( $taxonomy ) {
            ?>
            <div class="form-field term-logo-wrap">
                <label for="team_logo"><?php _e( 'Team Logo', 'fury-skater-gallery' ); ?></label>
                <div class="team-logo-field">
                    <input type="hidden" id="team_logo_id" name="team_logo_id" value="">
                    <p>
                        <button type="button" class="upload_image_button button button-secondary"><?php _e( 'Upload/Choose Image', 'fury-skater-gallery' ); ?></button>
                    </p>
                </div>
                <p><?php _e( 'The team logo image.', 'fury-skater-gallery' ); ?></p>
            </div>
            <?php
        }

        /**
         * Save the custom Team taxonomy fields.
         *
         * @param int $term_id The ID of the term being saved.
         */
        public function save_team_logo( $term_id ) {
            if ( isset( $_POST['team_logo_id'] ) ) {
                update_term_meta( $term_id, '_team_logo_id', sanitize_text_field( $_POST['team_logo_id'] ) );
            } else {
                delete_term_meta( $term_id, '_team_logo_id' );
            }
        }

        /**
         * Enqueue media uploader scripts for the Team taxonomy.
         *
         * @param string $hook The current admin page hook.
         */
        public function enqueue_team_logo_scripts( $hook ) {
            if ( ( 'edit-tags.php' === $hook || 'term.php' === $hook ) && isset( $_GET['taxonomy'] ) && 'team' === $_GET['taxonomy'] ) {
                wp_enqueue_media();
                wp_enqueue_script( 'fury-skater-gallery-team-logo', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/fury-skater-gallery-team-logo.js', array( 'jquery' ), $this->version, true );
            }
        }





        /**
 * Enqueue color picker scripts and styles for taxonomy pages.
 *
 * @param string $hook The current admin page hook.
 */
        public function enqueue_color_picker( $hook ) {
            if ( 'edit-tags.php' === $hook || 'term.php' === $hook ) {
                wp_enqueue_style( 'wp-color-picker' );
                wp_enqueue_script( 'fury-color-picker-script', plugin_dir_url( __FILE__ ) . 'js/fury-color-picker.js', array( 'wp-color-picker' ), $this->version, true );
            }
        }

        /**
         * Registers the 'skater' custom post type.
         */
        public function register_skater_cpt() {
            $labels = array(
                'name'                => _x( 'Skaters', 'Post Type General Name', 'fury-skater-gallery' ),
                'singular_name'       => _x( 'Skater', 'Post Type Singular Name', 'fury-skater-gallery' ),
                'menu_name'           => __( 'Skaters', 'fury-skater-gallery' ),
                'parent_item_colon'   => __( 'Parent Skater:', 'fury-skater-gallery' ),
                'all_items'           => __( 'All Skaters', 'fury-skater-gallery' ),
                'add_new_item'        => __( 'Add New Skater', 'fury-skater-gallery' ),
                'add_new'             => __( 'Add New', 'fury-skater-gallery' ),
                'new_item'            => __( 'New Skater', 'fury-skater-gallery' ),
                'edit_item'           => __( 'Edit Skater', 'fury-skater-gallery' ),
                'update_item'         => __( 'Update Skater', 'fury-skater-gallery' ),
                'view_item'           => __( 'View Skater', 'fury-skater-gallery' ),
                'view_items'          => __( 'View Skaters', 'fury-skater-gallery' ),
                'search_items'        => __( 'Search Skaters', 'fury-skater-gallery' ),
                'not_found'           => __( 'Not found', 'fury-skater-gallery' ),
                'not_found_in_trash'  => __( 'Not found in Trash', 'fury-skater-gallery' ),
                'featured_image'      => __( 'Skater Headshot', 'fury-skater-gallery' ),
                'set_featured_image'  => __( 'Set skater headshot', 'fury-skater-gallery' ),
                'remove_featured_image' => __( 'Remove skater headshot', 'fury-skater-gallery' ),
                'use_featured_image'  => __( 'Use as skater headshot', 'fury-skater-gallery' ),
                'insert_into_item'    => __( 'Insert into skater', 'fury-skater-gallery' ),
                'uploaded_to_this_item' => __( 'Uploaded to this skater', 'fury-skater-gallery' ),
                'items_list'          => __( 'Skaters list', 'fury-skater-gallery' ),
                'items_list_navigation' => __( 'Skaters list navigation', 'fury-skater-gallery' ),
                'filter_items_list'   => __( 'Filter skaters list', 'fury-skater-gallery' ),
            );
            $args = array(
                'labels'              => $labels,
                'public'              => true, // Not intended for direct public viewing as single posts
                'publicly_queryable'  => false,
                'show_ui'             => true, // Show in the admin interface
                'show_in_menu'        => 'fury-skater-gallery', // show under plugin menu, set to true to show as main admin menu item instead
                'query_var'           => true,
                'rewrite'             => array( 'slug' => 'skater' ),
                'capability_type'     => 'post',
                'hierarchical'        => false,
                'supports'            => array( 'title', 'thumbnail' ), // 'title' for skater name, 'thumbnail' for headshot
                'menu_icon'           => 'dashicons-users', // Choose an appropriate dashicon
            );
            register_post_type( 'skater', $args );
        }

        /**
         * Registers the 'team' custom taxonomy.
         */
        public function register_team_taxonomy() {
            $labels = array(
                'name'                       => _x( 'Teams', 'Taxonomy General Name', 'fury-skater-gallery' ),
                'singular_name'              => _x( 'Team', 'Taxonomy Singular Name', 'fury-skater-gallery' ),
                'menu_name'                  => __( 'Teams', 'fury-skater-gallery' ),
                'all_items'                  => __( 'All Teams', 'fury-skater-gallery' ),
                'parent_item'                => __( 'Parent Team', 'fury-skater-gallery' ),
                'parent_item_colon'          => __( 'Parent Team:', 'fury-skater-gallery' ),
                'new_item_name'              => __( 'New Team Name', 'fury-skater-gallery' ),
                'add_new_item'               => __( 'Add New Team', 'fury-skater-gallery' ),
                'edit_item'                  => __( 'Edit Team', 'fury-skater-gallery' ),
                'update_item'                => __( 'Update Team', 'fury-skater-gallery' ),
                'view_item'                  => __( 'View Team', 'fury-skater-gallery' ),
                'separate_items_with_commas' => __( 'Separate teams with commas', 'fury-skater-gallery' ),
                'add_or_remove_items'      => __( 'Add or remove teams', 'fury-skater-gallery' ),
                'choose_from_most_used'    => __( 'Choose from the most used teams', 'fury-skater-gallery' ),
                'popular_items'              => __( 'Popular Teams', 'fury-skater-gallery' ),
                'search_items'               => __( 'Search Teams', 'fury-skater-gallery' ),
                'not_found'                  => __( 'Not Found', 'fury-skater-gallery' ),
                'no_terms'                   => __( 'No teams', 'fury-skater-gallery' ),
                'items_list'                 => __( 'Teams list', 'fury-skater-gallery' ),
                'items_list_navigation'      => __( 'Teams list navigation', 'fury-skater-gallery' ),
            );
            $args = array(
                'labels'                     => $labels,
                'hierarchical'               => true, // Set to true if you want parent/child teams
                'public'                     => false,
                'show_ui'                    => true, // Show in the admin interface
                'show_admin_column'          => true, // Display as a column in the Skater post list
                'show_in_menu'               => 'edit.php?post_type=skater',
                'show_in_nav_menus'          => false,
                'show_tagcloud'              => false,
                'rewrite'                    => false,
            );
            register_taxonomy( 'team', 'skater', $args ); // Associate this taxonomy with the 'skater' CPT
        }

        public function register_moonlighting_team_taxonomy() {
            $labels = array(
                'name'                       => _x( 'Moonlighting Teams', 'Taxonomy General Name', 'fury-skater-gallery' ),
                'singular_name'              => _x( 'Moonlighting Team', 'Taxonomy Singular Name', 'fury-skater-gallery' ),
                'menu_name'                  => __( 'Moonlighting Teams', 'fury-skater-gallery' ),
                'all_items'                  => __( 'All Moonlighting Teams', 'fury-skater-gallery' ),
                'parent_item'                => __( 'Parent Moonlighting Team', 'fury-skater-gallery' ),
                'parent_item_colon'          => __( 'Parent Moonlighting Team:', 'fury-skater-gallery' ),
                'new_item_name'              => __( 'New Moonlighting Team Name', 'fury-skater-gallery' ),
                'add_new_item'               => __( 'Add New Moonlighting Team', 'fury-skater-gallery' ),
                'edit_item'                  => __( 'Edit Moonlighting Team', 'fury-skater-gallery' ),
                'update_item'                => __( 'Update Moonlighting Team', 'fury-skater-gallery' ),
                'view_item'                  => __( 'View Moonlighting Team', 'fury-skater-gallery' ),
                'separate_items_with_commas' => __( 'Separate Moonlighting teams with commas', 'fury-skater-gallery' ),
                'add_or_remove_items'      => __( 'Add or remove Moonlighting teams', 'fury-skater-gallery' ),
                'choose_from_most_used'    => __( 'Choose from the most used Moonlighting teams', 'fury-skater-gallery' ),
                'popular_items'              => __( 'Popular Moonlighting Teams', 'fury-skater-gallery' ),
                'search_items'               => __( 'Search Moonlighting Teams', 'fury-skater-gallery' ),
                'not_found'                  => __( 'Not Found', 'fury-skater-gallery' ),
                'no_terms'                   => __( 'No Moonlighting teams', 'fury-skater-gallery' ),
                'items_list'                 => __( 'Moonlighting Teams list', 'fury-skater-gallery' ),
                'items_list_navigation'      => __( 'Moonlighting Teams list navigation', 'fury-skater-gallery' ),
                'filter_by_item'             => __( 'Moonlighting Team', 'fury-skater-gallery' ),
            );
            $args = array(
                'labels'                     => $labels,
                'hierarchical'               => true, // Set to true if you want parent/child teams
                'public'                     => false,
                'show_ui'                    => true, // Show in the admin interface
                'show_admin_column'          => true, // Display as a column in the Skater post list
                'show_in_menu'               => 'edit.php?post_type=skater',
                //'show_in_menu'               => false,
                'show_in_nav_menus'          => false,
                'show_tagcloud'              => false,
                'rewrite'                    => false,
            );
            register_taxonomy( 'moonlighting_team', 'skater', $args );
        }

        public function sync_team_terms_to_moonlighting_team( $term_id, $tt_id, $taxonomy ) {
            if ( $taxonomy !== 'team' ) {
                return;
            }

            $term = get_term( $term_id, 'team' );
            if ( ! term_exists( $term->name, 'moonlighting_team' ) ) {
                wp_insert_term( $term->name, 'moonlighting_team', array(
                    'slug' => $term->slug,
                    'description' => $term->description,
                    'parent' => 0
                ) );
            }
        }

        public function sync_moonlighting_team_before_team_deletion( $term, $taxonomy ) {
            if ( 'team' !== $taxonomy ) {
        return $term; // Don't interfere with other taxonomy deletions
    }

    // Get the term object *before* deletion
    $team_to_delete = get_term( $term, 'team' );

    if ( $team_to_delete && ! is_wp_error( $team_to_delete ) && ! empty( $team_to_delete->slug ) ) {
        $moonlighting_term = get_term_by( 'slug', $team_to_delete->slug, 'moonlighting_team' );

        if ( $moonlighting_term && ! is_wp_error( $moonlighting_term ) ) {
            $deleted = wp_delete_term( $moonlighting_term->term_id, 'moonlighting_team' );
            if ( is_wp_error( $deleted ) ) {
                error_log( 'Error deleting moonlighting team term (pre_delete_term): ' . $deleted->get_error_message() );
            }
        }
    }

    return $term; // Important: Filters should always return the passed value (unless you want to prevent the action)
}






        /**
 * Add custom fields to the 'team' taxonomy edit form.
 *
 * @param WP_Term $term The term being edited.
 */
        public function add_team_color_field( $term ) {
    // Get the current color value
            $team_color = get_term_meta( $term->term_id, '_team_color', true );
            ?>
            <tr class="form-field term-color-wrap">
                <th scope="row"><label for="team_color"><?php esc_html_e( 'Team Color', 'fury-skater-gallery' ); ?></label></th>
                <td>
                    <input type="text" name="team_color" id="team_color" value="<?php echo esc_attr( $team_color ); ?>" class="fury-color-picker" data-default-color="#ffffff" />
                    <p class="description"><?php esc_html_e( 'Choose a color for this team.', 'fury-skater-gallery' ); ?></p>
                </td>
            </tr>
            <?php
        }

/**
 * Add custom fields to the 'team' taxonomy new term form.
 */
public function add_new_team_color_field() {
    ?>
    <div class="form-field term-color-wrap">
        <label for="team_color"><?php esc_html_e( 'Team Color', 'fury-skater-gallery' ); ?></label>
        <input type="text" name="team_color" id="team_color" value="" class="fury-color-picker" data-default-color="#ffffff" />
        <p><?php esc_html_e( 'Choose a color for this team.', 'fury-skater-gallery' ); ?></p>
    </div>
    <?php
}


    /**
 * Save the custom term meta for the 'team' taxonomy.
 *
 * @param int $term_id The ID of the term being saved.
 */
    public function save_team_color( $term_id ) {
        if ( isset( $_POST['team_color'] ) ) {
            $team_color = sanitize_hex_color( $_POST['team_color'] );
            update_term_meta( $term_id, '_team_color', $team_color );
        }
    }


        /**
         * Adds meta boxes to the 'skater' post type.
         */
        public function add_skater_meta_boxes() {
            add_meta_box(
                'skater_details',
                __( 'Skater Details', 'fury-skater-gallery' ),
                array( $this, 'render_skater_meta_box' ), // Use $this to call the method
                'skater',
                'normal',
                'high'
            );
        } 

        /**
         * Renders the content of the 'Skater Details' meta box.
         *
         * @param WP_Post $post The current post object.
         */
        public function render_skater_meta_box( $post ) {
            // Add a nonce field for security
            wp_nonce_field( 'fury_skater_gallery_save_skater_meta', 'fury_skater_gallery_skater_meta_nonce' );

            // Get the current values of the meta fields
            //$bio_content  = get_post_meta( $post->ID, '_bio_content', true );
            $skater_number = get_post_meta( $post->ID, '_skater_number', true );

            ?>
            <p>
                <label for="skater_number"><?php esc_html_e( 'Skater Number:', 'fury-skater-gallery' ); ?></label>
                <input type="text" id="skater_number" name="skater_number" value="<?php echo esc_attr( $skater_number ); ?>" class="widefat">
                <span class="description"><?php esc_html_e( 'Enter the skater\'s number.', 'fury-skater-gallery' ); ?></span>
            </p>
            
            <?php
        }

        /**
         * Saves the custom meta fields for the 'skater' post type.
         *
         * @param int $post_id The ID of the post being saved.
         */
        public function save_skater_meta( $post_id ) {
            // Check if our nonce is set and valid.
            if ( ! isset( $_POST['fury_skater_gallery_skater_meta_nonce'] ) || ! wp_verify_nonce( $_POST['fury_skater_gallery_skater_meta_nonce'], 'fury_skater_gallery_save_skater_meta' ) ) {
                return;
            }

            // If this is an autosave, our form has not been submitted, so we don't want to do anything.
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return;
            }

            // Check the user's permissions.
            if ( isset( $_POST['post_type'] ) && 'skater' === $_POST['post_type'] ) {
                if ( ! current_user_can( 'edit_post', $post_id ) ) {
                    return;
                }
            }

            // Sanitize and save the biography content
            /*if ( isset( $_POST['bio_content'] ) ) {
                $bio_content = wp_kses_post( $_POST['bio_content'] );
                update_post_meta( $post_id, '_bio_content', $bio_content );
            }*/
            // Sanitize and save the skater number.
            if ( isset( $_POST['skater_number'] ) ) {
                $skater_number = sanitize_text_field( $_POST['skater_number'] );
                update_post_meta( $post_id, '_skater_number', $skater_number );
            }
        }

        /**
 * Add custom columns to the 'team' taxonomy admin list.
 *
 * @param array $columns Array of existing columns.
 * @return array Modified array of columns.
 */
        public function add_team_color_column( $columns ) {
            $new_columns = array();
            foreach ( $columns as $key => $title ) {
                $new_columns[ $key ] = $title;
        if ( 'slug' === $key ) { // Insert our new columns after the 'Slug' column
        $new_columns['team_logo'] = __( 'Logo', 'fury-skater-gallery' );
        $new_columns['team_color'] = __( 'Color', 'fury-skater-gallery' );
    }
}
return $new_columns;
}

/**
 * Populate the custom columns on the 'team' taxonomy admin list.
 *
 * @param string $content The default column content.
 * @param string $column The name of the column being displayed.
 * @param int $term_id The ID of the term being displayed.
 */
public function manage_team_logo_column( $content, $column, $term_id ) {
    if ( 'team_logo' === $column ) {
        $logo_id = get_term_meta( $term_id, '_team_logo_id', true );
        if ( $logo_id ) {
            $thumbnail_url = wp_get_attachment_image_src( $logo_id, array( 50, 50 ) ); // Get a 50x50 thumbnail
            if ( $thumbnail_url ) {
                echo '<img src="' . esc_url( $thumbnail_url[0] ) . '" alt="' . esc_attr__( 'Team Logo', 'fury-skater-gallery' ) . '" width="50" height="50" style="vertical-align: middle;" />';
            } else {
                echo '&mdash;'; // Show a dash if the thumbnail can't be retrieved
            }
        } else {
            echo '&mdash;'; // Show a dash if no logo is set
        }
    }
}

/**
 * Populate the custom columns on the 'team' taxonomy admin list.
 *
 * @param string $column The name of the column being displayed.
 * @param int $term_id The ID of the term being displayed.
 */
public function manage_team_color_column( $content, $column, $term_id ) {
    if ( 'team_color' === $column ) {
        $team_color = get_term_meta( $term_id, '_team_color', true );
        if ( $team_color ) {
            echo '<span style="display: inline-block; width: 20px; height: 20px; border: 1px solid #ccc; background-color: ' . esc_attr( $team_color ) . '; vertical-align: middle; margin-right: 5px;"></span>';
            echo '<span style="vertical-align: middle;">' . esc_html( $team_color ) . '</span>';
        } else {
            echo '&mdash;'; // Show a dash if no color is set
        }
    }
}

/**
 * Add custom columns to the Skater post type admin list.
 *
 * @param array $columns Array of existing columns.
 * @return array Modified array of columns.
 */
public function add_skater_number_column( $columns ) {
    $new_columns = array();
    foreach ( $columns as $key => $title ) {
        $new_columns[ $key ] = $title;
        if ( 'title' === $key ) { // Insert our column after the 'Title' column
        $new_columns['skater_number'] = __( 'Number', 'fury-skater-gallery' );
        }
        if ( 'taxonomy-team' === $key ) { // Target the auto-generated 'team' taxonomy column
            $new_columns['taxonomy-team'] = __( 'Team', 'fury-skater-gallery' ); // Change the label to singular
            $new_columns['bouting_status'] = __( 'Bouting Status', 'fury-skater-gallery' );
        }
        if ( 'taxonomy-moonlighting_team' === $key ) { // Target the auto-generated 'team' taxonomy column
            $new_columns['taxonomy-moonlighting_team'] = __( 'Moonlighting Team', 'fury-skater-gallery' ); // Change the label to singular
            $new_columns['moonlighting_status'] = __( 'Moonlighting Status', 'fury-skater-gallery' );
        }

    }
    return $new_columns;
}

/**
 * Populate the custom columns on the Skater post type admin list.
 *
 * @param string $column The name of the column being displayed.
 * @param int $post_id The ID of the post being displayed.
 */
public function manage_skater_number_column( $column, $post_id ) {
    if ( 'skater_number' === $column ) {
        $skater_number = get_post_meta( $post_id, '_skater_number', true );
        echo esc_html( $skater_number );
    }
    if ( 'bouting_status' === $column ) {
        $bouting_status = get_post_meta($post_id, 'roster_status_group_bouting_status', true);
        echo $bouting_status ? esc_html($bouting_status) : '-';
    }
    if ( 'moonlighting_status' === $column ) {
        $moonlighting_status = get_post_meta($post_id, 'roster_status_group_moonlighting_status', true);
        echo $moonlighting_status ? esc_html($moonlighting_status) : '-';
    }
}

/**
 * Make the custom columns sortable.
 *
 * @param array $columns An array of sortable columns.
 * @return array An updated array of sortable columns.
 */
public function make_skater_number_sortable( $columns ) {
    $columns['skater_number'] = '_skater_number'; // The meta key to sort by
    $columns['taxonomy-team'] = 'team';          // 'taxonomy-team' is the column key, 'team' is the taxonomy slug
    // $columns['taxonomy-moonlighting_team'] = 'moonlighting_team';
    return $columns;
}
public function skater_posts_orderby_custom( $query ) {
    if ( ! is_admin() || ! $query->is_main_query() ) {
        return;
    }

    $orderby = $query->get( 'orderby' );
    $order   = $query->get( 'order' ); // Get the current order (ASC or DESC)

    if ('_skater_number' === $orderby ) {
        $query->set( 'orderby', 'meta_value' );
        $query->set( 'meta_key', '_skater_number');
        $query->set( 'meta_type', 'numeric' );
    }
    
    if ( 'team' === $orderby ) {
        $query->set( 'orderby', 'name' ); // sort by the term name

        // Dynamically set the order (ASC or DESC)
        $query->set( 'order', $order ); // Apply the order direction (ASC/DESC)
        

        // Temporarily add filters for this request
        add_filter( 'posts_join', [ $this, 'join_team_taxonomy_table' ] );
        add_filter( 'posts_orderby', [ $this, 'orderby_team_taxonomy_name' ] );
        add_filter( 'posts_groupby', [ $this, 'groupby_post_id' ] );

        // Schedule removal immediately after they run
        $instance = $this; // capture $this for use inside closure

        add_filter( 'the_posts', function( $posts ) use ( $instance ) {
            remove_filter( 'posts_join', [ $instance, 'join_team_taxonomy_table' ] );
            remove_filter( 'posts_orderby', [ $instance, 'orderby_team_taxonomy_name' ] );
            remove_filter( 'posts_groupby', [ $instance, 'groupby_post_id' ] );
            return $posts;
        }, 999 );

    }
}

public function join_team_taxonomy_table( $join ) {
    global $wpdb;
    $join .= " LEFT JOIN {$wpdb->term_relationships} AS tr ON ({$wpdb->posts}.ID = tr.object_id)";
    $join .= " LEFT JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy = 'team')";
    $join .= " LEFT JOIN {$wpdb->terms} AS t ON (tt.term_id = t.term_id)";
    return $join;
}

public function orderby_team_taxonomy_name( $orderby ) {
    // Retrieve the 'order' query variable (ASC or DESC)
    $order = strtoupper( get_query_var( 'order' ) ); // Make sure it's either ASC or DESC
    $order = in_array( $order, ['ASC', 'DESC'], true ) ? $order : 'ASC'; // Default to ASC if invalid

    return "t.name {$order}"; // Use dynamic order direction
}


public function groupby_post_id( $groupby ) {
    global $wpdb;
    return "{$wpdb->posts}.ID";
}

/**
 * Add filter by team dropdown
 * 
 * */
public function add_team_filter_dropdown() {
    global $typenow;

    // Only add the filter for the 'skater' post type
    if ( 'skater' === $typenow ) {
        // Get all terms in the 'team' taxonomy
        $teams = get_terms( array(
            'taxonomy' => 'team',
            'hide_empty' => false, // Show even empty terms
        ) );

        // Add the dropdown
        echo '<select name="team_filter" id="team_filter" class="postform">';
        echo '<option value="">Filter by Team</option>'; // Option for "all teams"
        foreach ( $teams as $team ) {
            // Select the option if the current filter matches this team
            $selected = ( isset( $_GET['team_filter'] ) && $_GET['team_filter'] == $team->term_id ) ? ' selected="selected"' : '';
            echo '<option value="' . esc_attr( $team->term_id ) . '"' . $selected . '>' . esc_html( $team->name ) . '</option>';
        }
        echo '</select>';
    }
}
public function filter_posts_by_team( $query ) {
    if ( ! is_admin() || ! $query->is_main_query() ) {
        return;
    }

    // Check if we're on the skater post type list page and a team filter is selected
    if ( 'skater' === $query->get( 'post_type' ) && isset( $_GET['team_filter'] ) && ! empty( $_GET['team_filter'] ) ) {
        $team_id = $_GET['team_filter'];

        // Modify the query to filter by the selected team term
        $query->set( 'tax_query', array(
            array(
                'taxonomy' => 'team',
                'field'    => 'term_id',
                'terms'    => $team_id,
                'operator' => 'IN',
            ),
        ) );
    }
}



/**
 * Add bouting status and moonlighting status to quick edit
 * 
 * */
function add_skater_quick_edit_fields($column_name, $post_type) {
    if ($post_type !== 'skater') return;

    if (in_array($column_name, ['bouting_status', 'moonlighting_status'])) {
        ?>
        <fieldset class="inline-edit-col-right">
            <div>
                <div>
                    <label>
                        <span><?php echo ucfirst(str_replace('_', ' ', $column_name)); ?></span>
                        <select name="<?php echo esc_attr($column_name); ?>">
                            <option value="">— No Change —</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </label>
                </div>
            </div>
        </fieldset>
        <?php
    }
}

function skater_status_quick_edit_save( $post_id ) {

    $post_type = get_post_type( $post_id );
    if ($post_type !== 'skater') return;

    // Check for proper nonce and permissions
    // if ( ! wp_verify_nonce( $_POST['_inline_edit'], 'inlineeditnonce')){
    //     return;
    // }
    if ( ! isset( $_POST['_inline_edit'] ) || ! wp_verify_nonce( $_POST['_inline_edit'], 'inlineeditnonce')) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Sanitize and save bouting_status
    if ( isset( $_POST['bouting_status'] ) ) {
        $bouting_status = sanitize_text_field( $_POST['bouting_status'] );
        update_post_meta( $post_id, 'roster_status_group_bouting_status', $bouting_status );
    }

    // Sanitize and save moonlighting_status
    if ( isset( $_POST['moonlighting_status'] ) ) {
        $moonlighting_status = sanitize_text_field( $_POST['moonlighting_status'] );
        update_post_meta( $post_id, 'roster_status_group_moonlighting_status', $moonlighting_status );
    }
}

function save_skater_bulk_edit() {
    // Check nonce and permissions
    check_ajax_referer( 'skater_bulk_edit_nonce', '_ajax_nonce' );

    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( 'Unauthorized' );
    }

    $post_ids = isset($_POST['post_ids']) ? json_decode(stripslashes($_POST['post_ids'])) : [];

    $bouting_status = isset($_POST['bouting_status']) ? sanitize_text_field($_POST['bouting_status']) : '';
    $moonlighting_status = isset($_POST['moonlighting_status']) ? sanitize_text_field($_POST['moonlighting_status']) : '';

    foreach ( $post_ids as $post_id ) {
        if ( get_post_type( $post_id ) !== 'skater' ) continue;

        if ( $bouting_status !== '' ) {
            update_post_meta( $post_id, 'roster_status_group_bouting_status', $bouting_status );
        }

        if ( $moonlighting_status !== '' ) {
            update_post_meta( $post_id, 'roster_status_group_moonlighting_status', $moonlighting_status );
        }
    }

    wp_send_json_success( 'Bulk edit saved' );
}








}

}