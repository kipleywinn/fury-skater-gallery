<?php

$moonlighting_taxonomy_link = admin_url( 'edit-tags.php?taxonomy=moonlighting_team&post_type=skater' );
$team_taxonomy_link = admin_url( 'edit-tags.php?taxonomy=team&post_type=skater' );
$admin_settings_link = admin_url( 'admin.php?page=fury-skater-gallery-settings' );

?>
<div class="wrap">
  <h1>Fury Skater Gallery – Help</h1>
  <p>Use the shortcode <code>[fury-skater-gallery]</code> to display the skater gallery.</p>

  <details open>
    <summary><h2>📌 Shortcode Options</h2></summary>
    <dl>
      <dt><code>type</code></dt>
      <dd>
        Filter by content type: <code>skaters</code>, <code>crew</code>, or leave empty for all.<br>
        <strong>Example:</strong> <code>[fury-skater-gallery type="skaters"]</code>
      </dd>

      <dt><code>team</code></dt>
      <dd>
        Filter by specific team slug. Overrides the <code>type</code> filter.<br>
        Find slugs on the <a href="<?php echo esc_url( $team_taxonomy_link ) ?>">Team taxonomy page</a>.<br>
        <strong>Example:</strong> <code>[fury-skater-gallery team="thrash-pandas"]</code>
      </dd>

      <dt><code>roster</code></dt>
      <dd>
        Defaults to <code>false</code>. Set to <code>true</code> to show only skaters with a Bouting Status of <em>yes</em>.<br>
        <strong>Example:</strong> <code>[fury-skater-gallery roster="true"]</code><br>
        Can be combined with a team to show Bouting Skaters <em>and</em> Moonlighting Skaters for that team: <br>
        <strong>Example:</strong> <code>[fury-skater-gallery team="x-force" roster="true"]</code>
      </dd>
    </dl>
  </details>

  <details>
    <summary><h2>🏷️ Team Setup</h2></summary>
    <p>
      Each skater should be assigned to a team using the <strong>Team</strong> taxonomy and, if applicable, a <strong>Moonlighting Team</strong>.
    </p>
    <ul>
      <li>Only <strong>published</strong> skaters appear in the gallery.</li>
      <li>You can assign a team color via the <a href="<?php echo esc_url( $team_taxonomy_link ) ?>">Team taxonomy page</a>.</li>
      <li>Moonlighting Teams are synced automatically with Team additions/removals.</li>
      <li>You can <a href="<?php echo esc_url( $admin_settings_link ) ?>">enable access</a> to Moonlighting Teams for troubleshooting.</li>
    </ul>
    <p>Adding a team called "Crew" with the slug <code>crew</code> will enable you to separate <code>type="crew"</code> from <code>type="skaters"</code> in your shortcode.</p>
  </details>

  <details>
    <summary><h2>🖼️ Displaying the Gallery</h2></summary>
    <p>The gallery appears as a responsive card grid with team filters and bio popups.</p>
    <ul>
      <li>Team colors appear on borders and overlays automatically.</li>
      <li>You can display <strong>multiple galleries</strong> on a page.</li>
      <li>To separate skaters and crew, use two shortcodes like:<br>
        <code>[fury-skater-gallery type="skaters"]</code><br>
        <code>[fury-skater-gallery type="crew"]</code>
      </li>
    </ul>
  </details>
</div>
