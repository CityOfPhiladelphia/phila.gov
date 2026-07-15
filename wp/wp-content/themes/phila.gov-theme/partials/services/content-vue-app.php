<?php
/*
 *
 * Rendering for vuejs template
 *
 */
?>

<?php
  $vue_app_urls = rwmb_meta('phila_vue_app', '', $post->ID);
  $app_id = isset($vue_app_urls['phila_vue_app_id']) ? $vue_app_urls['phila_vue_app_id'] : 'vue-app';
  $app_title = rwmb_meta('vue_app_title');
  $page_rows = rwmb_meta('phila_row');

    foreach ($page_rows as $key => $value):
      $current_row = $page_rows[$key];?>
      <?php if ( ( isset( $current_row['phila_grid_options'] ) && $current_row['phila_grid_options'] == 'phila_grid_options_full' ) &&
      isset( $current_row['phila_full_options']['phila_full_options_select'] ) ):
      // Begin full width row
      $current_row_option = $current_row['phila_full_options']['phila_full_options_select'];
      
      if ( $current_row_option === 'phila_announcements' ): ?>
          <!-- Announcement Content -->
          <?php $ann_cat_override = isset( $current_row['phila_full_options']['phila_announcements_group']['phila_ann_category']) ? $current_row['phila_full_options']['phila_announcements_group']['phila_ann_category'] : ''; ?>
          <?php $ann_tag_override = isset( $current_row['phila_full_options']['phila_announcements_group']['phila_ann_tag']) ? $current_row['phila_full_options']['phila_announcements_group']['phila_ann_tag'] : ''; ?>
            <?php include( locate_template( 'partials/global/phila_full_row_announcements.php' ) ); ?>
          <!-- /Announcement Content -->
      <?php endif; ?>
      <?php endif; ?>
    <?php endforeach; ?>

    <?php if (is_array($vue_app_urls)) {
      $count = 1;
      foreach($vue_app_urls['phila-vue-app-js'] as $url) {
        $handle = $post->post_name . '-vue-app-js-url-' . $count . $app_title;
        wp_enqueue_script($handle, $url['phila_vue_app_js_url'], array(), null, true );
        $count++;
      }
      $count = 1;
      foreach($vue_app_urls['phila-vue-app-css'] as $url) {
        $handle = $post->post_name . '-vue-app-css-url-' . $count . $app_title;
        wp_enqueue_style($handle, $url['phila_vue_app_css_url']);
        $count++;
    }
  }
    
    ?>
  <!--Vuejs-->
  <?php get_template_part( 'partials/content', 'custom-markup-before-wysiwyg' ); ?>
  <?php include( locate_template( 'partials/content-basic.php' ) ); ?>
  <?php get_template_part( 'partials/content', 'custom-markup-after-wysiwyg' ); ?>
  <?php include(locate_template( 'partials/vue-apps/app-container.php' ) ); ?>
  <!--/Vuejs-->

<?php include(locate_template('partials/content-additional.php')); ?>
