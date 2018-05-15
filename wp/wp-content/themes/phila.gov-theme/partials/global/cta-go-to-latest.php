<?php
/* Template for "go to the latest" button */

$the_latest = array(
  'post_type' => 'page',
  'meta_key' => '_wp_page_template',
  'meta_value' => 'templates/the-latest.php',
  'field' => 'ids'
);
$latest_desc = get_posts( $the_latest );
$desc = rwmb_meta('phila_meta_desc', $args = null, $latest_desc[0]->ID );
?>
<div class="row mvxl">
  <div class="columns panel">
    <div class="row equal-height">
      <div class="small-24 medium-16 columns valign equal">
        <div class="valign-cell">
          <h3 class="mbn">More from the City of Philadelphia</h3>
          <p class="mts"><?php echo $desc ?></p>
        </div>
      </div>
      <div class="small-24 medium-8 columns valign equal center">
        <a href="/the-latest" class="button full mts">Go to The latest</a>
      </div>
    </div>
  </div>
</div>
