<?php
/*
 *
 * Rendering for vuejs template
 *
 */
?>

<?php
  $vue_app_urls = rwmb_meta('phila_vue_app', '', $post->ID);

  $app_title = rwmb_meta('vue_app_title');
    if (is_array($vue_app_urls)) {
      $count = 1;
      foreach($vue_app_urls['phila-vue-app-js'] as $url) {
        $handle = $post->post_name . '-vue-app-js-url-' . $count;
        wp_enqueue_script($handle, $url['phila_vue_app_js_url'], array(), null, true );
        $count++;
      }
      $count = 1;
      foreach($vue_app_urls['phila-vue-app-css'] as $url) {
        $handle = $post->post_name . '-vue-app-css-url-' . $count;
        wp_enqueue_style($handle, $url['phila_vue_app_css_url']);
        $count++;
    }
  }
    
    ?>
  <!--Vuejs-->
  <?php if (isset($app_title) ): ?>
    <div class="grid-container">
      <div class="grid-x">
        <div class="cell small-24">
          <h2 class="contrast"><?php echo $app_title ?> </h2>
          </div>
      </div>
    </div>
  <?php endif;?>
  <div class="grid-container">
    <div class="grid-x">
      <div class="cell small-24">
        <div id="vue-app"></div>
      </div>
    </div>
  </div>
  <!--/Vuejs-->