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

    if (is_array($vue_app_urls)) {
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
  <div class="row">
    <div class="columns">
      <?php the_content(); ?>
    </div>
  </div>
  <?php get_template_part( 'partials/content', 'custom-markup-after-wysiwyg' ); ?>
  <?php include(locate_template( 'partials/vue-apps/app-container.php' ) ); ?>
  <!--/Vuejs-->

<?php include(locate_template('partials/content-additional.php')); ?>
