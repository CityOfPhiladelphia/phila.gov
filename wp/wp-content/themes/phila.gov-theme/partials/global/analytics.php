<?php 
  /* Template for Google Analytics */
?>
<?php 
    $lang = empty(rwmb_meta('phila_select_language', '', $post->ID)) ? 'english' : rwmb_meta('phila_select_language', '', $post->ID);

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
        "templateType": "<?php echo phila_get_selected_template() ?>",
      <?php if ( is_single() && get_post_type() === 'post') : ?>
        "articleTitle": "<?php echo get_the_title() ?>",
        "articleAuthor": "<?php echo get_the_author_meta('display_name') ?>",
        "publish": "<?php echo get_the_date() ?>",
        "articleCategory": "<?php echo phila_get_selected_template() ?>",
        "articleLanguage": "<?php echo $lang; ?>"
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
      "programAudience": "<?php echo implode (', ', $audiences); ?>",
      "programCategory": "<?php  echo implode(', ', $categories); ?>",
      <?php endif; ?>
      <?php if ( get_post_type() === 'service_page') :
      $category = get_the_terms( $post->ID, 'service_type' );
      $categories = array();
      if (!empty($category)) {
        foreach($category as $c){
          $categories[] = $c->name;
        }
      }?>
      "serviceCategory": "<?php  echo implode(', ', $categories); ?>",
      <?php endif; ?>

      });
    </script>

    <?php endif; ?>
    <!-- End Google Tag Manager DataLayer -->
  <!-- Google Tag Manager --> 
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start': new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0], j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src= 'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f); })(window,document,'script','dataLayer','GTM-MC6CR2');</script> 
  <!-- End Google Tag Manager -->
  
  <!-- Begin Microsoft Clarity -->
  <script type="text/javascript">
    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
    })(window, document, "clarity", "script", "4l8dhsl6kn");
  </script>
  <!-- End Microsoft Clarity -->
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-NHET8T5XY8"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-NHET8T5XY8');
  </script>
  <!-- End Global site tag (gtag.js) - Google Analytics -->
