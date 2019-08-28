<?php 
  /* Template for Google Analytics */
?>
<?php 
    if ( !is_archive() && !is_tax() && !is_home() && !is_404() ) : ?>
    <!-- Google Tag Manager DataLayer -->
    <?php $category = get_the_category();
      $departments = phila_get_current_department_name( $category, $byline = false, $break_tags = false, $name_list = true );
    ?>
    <script>
      window.dataLayer = window.dataLayer || [];
      dataLayer.push({
        "contentModifiedDepartment": "<?php echo $departments ?>",
        "lastUpdated": "<?php the_modified_time('Y-m-d H:i:s'); ?>",
        "templateType": "<?php echo phila_get_selected_template() ?>"
      });
    </script>
    <?php if ( is_single() && get_post_type() === 'post') : ?>
      <script>
        dataLayer.push({
          "articleTitle": "<?php echo get_the_title() ?>",
          "articleAuthor": "<?php echo get_the_author_meta('display_name') ?>",
          "publish": "<?php echo get_the_date() ?>",
          "articleCategory": "<?php echo phila_get_selected_template() ?>"
        });
      </script>
    <?php endif; ?>
    <?php if ( get_post_type() === 'programs' && phila_get_selected_template() === 'prog_landing_page'): 
      $category = get_the_terms( $post->ID, 'service_type' );
      $categories = array();
      if (!empty($category)) {
        foreach($category as $c){
          $categories[] = $c->name;
        }
      }
      $audience = get_the_terms( $post->ID, 'audience' ); 
      $audiences = array();
      if (!empty($audience)) {
        foreach($audience as $a){
          $audiences[] = $a->name;
        } 
      }?>
      <script>
        dataLayer.push({
          "programAudience": "<?php echo implode (', ', $audiences); ?>",
          "programCategory": "<?php  echo implode(', ', $categories); ?>",
        });
      </script>
    <?php endif; ?>
    <?php if ( get_post_type() === 'service_page') :
      $category = get_the_terms( $post->ID, 'service_type' );
      $categories = array();
      if (!empty($category)) {
        foreach($category as $c){
          $categories[] = $c->name;
        }
      }?>
      <script>
        dataLayer.push({
          "serviceCategory": "<?php  echo implode(', ', $categories); ?>",
        });
      </script>
    <?php endif; ?>
    <!-- End Google Tag Manager DataLayer -->
  <?php endif; ?>
  <!-- Google Tag Manager --> 
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0], j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src= 'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f); })(window,document,'script','dataLayer','GTM-MC6CR2');</script> 
  <!-- End Google Tag Manager -->