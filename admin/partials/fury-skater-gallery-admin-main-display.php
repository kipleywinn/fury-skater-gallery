<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://rockstarrollerderby.com
 * @since      1.0.0
 *
 * @package    Fury_Skater_Gallery
 * @subpackage Fury_Skater_Gallery/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">
  <h1>Fury Skater Gallery</h1>
  <p>Welcome to the plugin! Choose where you’d like to go:</p>

  <div class="fury-dashboard-cards">
    <!-- Existing Cards -->
    <div class="fury-card">
      <h2>🧑‍🤝‍🧑 Manage Skaters</h2>
      <p>Add, edit, or remove skaters for your league.</p>
      <a href="edit.php?post_type=skater" class="button button-primary">Go to Skaters</a>
    </div>
    <div class="fury-card">
      <h2>🏳️ Manage Teams</h2>
      <p>Create and assign teams to your skaters.</p>
      <a href="edit-tags.php?taxonomy=team&post_type=skater" class="button">Go to Teams</a>
    </div>
    <div class="fury-card">
      <h2>⚙️ Plugin Settings</h2>
      <p>Customize how the gallery appears on the front end.</p>
      <a href="admin.php?page=fury-skater-gallery-settings" class="button">View Settings</a>
    </div>
    <div class="fury-card">
      <h2>❓ Help & Shortcodes</h2>
      <p>Instructions for using the shortcode and customizing output.</p>
      <a href="admin.php?page=fury-skater-gallery-help" class="button">View Help</a>
    </div>
  </div>

  <hr>

  <h2>📋 Skaters by Team</h2>
  <p>Here’s a quick overview of skaters grouped by their team.</p>
  <p>Skaters not actively rostered (bouting status of "no") are grayed out. Only published skaters are listed; draft skaters not shown. Click on a skater to edit them.</p>

  <div class="fury-team-gallery">
    <?php
    $teams = get_terms( array(
      'taxonomy' => 'team',
      'hide_empty' => false,
    ) );

  // Sort teams alphabetically but push "Crew" to the end
    usort($teams, function($a, $b) {
      if (strtolower($a->name) === 'crew') return 1;
      if (strtolower($b->name) === 'crew') return -1;
      return strcmp($a->name, $b->name);
    });

    foreach ( $teams as $team ) {
    // Skip "Crew" during this loop — we'll handle it separately
      if (strtolower($team->name) === 'crew') {
        continue;
      }

      $team_logo_id = get_term_meta( $team->term_id, '_team_logo_id', true );
      $team_color = get_term_meta( $team->term_id, '_team_color', true ) ?: '#ccc';
      $team_logo = $team_logo_id ? wp_get_attachment_image( $team_logo_id, 'thumbnail' ) : '';

      echo '<div class="fury-team-card" style="border-color:' . esc_attr( $team_color ) . ';">';

      if ( $team_logo ) {
        echo '<div class="fury-team-logo">' . $team_logo . '</div>';
      }

      echo '<h3 style="color:' . esc_attr( $team_color ) . ';">' . esc_html( $team->name ) . '</h3>';

      $skaters = get_posts( array(
        'post_type' => 'skater',
        'tax_query' => array(
          array(
            'taxonomy' => 'team',
            'field'    => 'term_id',
            'terms'    => $team->term_id,
          ),
        ),
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
      ) );

      $moonlighting_skaters = get_posts( array(
        'post_type' => 'skater',
        'tax_query' => array(
          array(
            'taxonomy' => 'moonlighting_team',
            'field'    => 'term_id',
            'terms'    => $team->term_id,
          ),
        ),
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        'meta_query' => array(
          array(
            'key'     => 'roster_status_group_moonlighting_status',
            'value'   => 'yes',
            'compare' => '=',
          ),
        ),
      ) );

      if ( $skaters ) {
        echo '<ul class="fury-skater-list">';
        $count = 1;
        foreach ( $skaters as $skater ) {
        //var_dump( get_post_meta( $skater->ID ) );
          $number = get_post_meta( $skater->ID, '_skater_number', true );
          $bouting_status = get_post_meta( $skater->ID, 'roster_status_group_bouting_status', true );
          $edit_link = get_edit_post_link( $skater->ID );

          if ( 'yes' === $bouting_status ) {
            echo '<span class="bouting-status-yes">';
          }
          if ( 'no' === $bouting_status || '' === $bouting_status ) {
            echo '<span class="bouting-status-no">';
          }

          echo '<li>' .'<a class="skater-list-link" href="' . esc_url( $edit_link ) . '">' . $count++ . '. ' . esc_html( get_the_title( $skater ) ) . ' #' . esc_html( $number ) . '</a>';

        // if ( 'no' === $bouting_status || '' === $bouting_status ) {
        //   echo ' <span class="bouting-status">[not rostered]</span>';
        // }

          echo '</li></span>';

        }
        echo '</ul>';

      } else {
        echo '<p><em>No skaters assigned.</em></p>';
      }

     // Display moonlighting skaters for this team
      $moonlighting = get_posts( array(
       'post_type' => 'skater',
       'tax_query' => array(
        array(
         'taxonomy' => 'moonlighting_team',
         'field'    => 'slug',
         'terms'    => $team->slug,
       ),
      ),
       'meta_query' => array(
        array(
         'key'  => 'roster_status_group_moonlighting_status',
         'value' => 'yes',
         'compare' => '=',
       ),
      ),
       'posts_per_page' => -1,
       'orderby' => 'title',
       'order' => 'ASC',
     ) );

      if ( $moonlighting ) {
        echo '<div class="moonlighter-div"><h4>Moonlighters</h4>';
  echo '<ul class="fury-skater-list moonlighting-list">'; // extra class if you want
  $count = 1;
  foreach ( $moonlighting as $moon ) {
    $number = get_post_meta( $moon->ID, '_skater_number', true );
    $edit_link = get_edit_post_link( $moon->ID );

    echo '<li class="moonlighting-skater">'; // for styling separately
    echo '<a class="skater-list-link" href="' . esc_url( $edit_link ) . '">' . $count++ . '. ' . esc_html( get_the_title( $moon ) );

    if ( $number ) {
      echo ' #' . esc_html( $number );
    }

    echo '</a></li>';
  }
  echo '</ul></div>';
}

    echo '</div>'; // end fury-team-card
  }

  // CREW: Skaters assigned to the "Crew" team
  $crew_term = get_term_by( 'name', 'Crew', 'team' );

  if ( $crew_term && ! is_wp_error( $crew_term ) ) {
    $crew = get_posts( array(
      'post_type' => 'skater',
      'tax_query' => array(
        array(
          'taxonomy' => 'team',
          'field'    => 'term_id',
          'terms'    => $crew_term->term_id,
        ),
      ),
      'posts_per_page' => -1,
      'orderby' => 'title',
      'order' => 'ASC',
    ) );

    if ( $crew ) {
      $team_logo_id = get_term_meta( $crew_term->term_id, '_team_logo_id', true );
      $team_color = get_term_meta( $crew_term->term_id, '_team_color', true ) ?: '#333';
      $team_logo = $team_logo_id ? wp_get_attachment_image( $team_logo_id, 'thumbnail' ) : '';

      echo '<div class="fury-team-card" style="border-color:' . esc_attr( $team_color ) . ';">';

      if ( $team_logo ) {
        echo '<div class="fury-team-logo">' . $team_logo . '</div>';
      }

      echo '<h3 style="color:' . esc_attr( $team_color ) . ';">Crew</h3>';
      echo '<ul class="fury-skater-list">';
      $count = 1;
      foreach ( $crew as $person ) {
        $number = get_post_meta( $person->ID, '_skater_number', true );
        $edit_link = get_edit_post_link( $person->ID );

      // echo '<li>' . $count++ . '. ' . esc_html( $number ) . ' ' . esc_html( get_the_title( $person ) ) . '</li>';

        echo '<li>' .'<a class="skater-list-link" href="' . esc_url( $edit_link ) . '">' . $count++ . '. ' . esc_html( get_the_title( $person ) );

        if ($number) {
          echo ' #' . esc_html( $number );
        }

        echo '</a>';

      }
      echo '</ul>';
      echo '</div>';
    }
  }

  ?>
</div>

</div>
