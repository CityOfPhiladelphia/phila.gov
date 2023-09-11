<?php 
if ( null !== rwmb_meta( 'phila_stub_source' ) ) : 
  $stub_source = rwmb_meta( 'phila_stub_source' );
  $post_id = intval( $stub_source );

    $stub_args = array(
      'p' => $post_id,
      'post_type' => 'service_page'
    ); 

  $stub_post = new WP_Query($stub_args); 
  if ( $stub_post->have_posts() ): 
    while ( $stub_post->have_posts() ) : 
      $stub_post->the_post(); 
      $source_template =  rwmb_meta( 'phila_template_select'); 
      if ($source_template == 'custom_content') :
        get_template_part('partials/content', 'basic'); 
        include( locate_template( 'partials/content-phila-row.php' ) ); 
      elseif ($source_template == 'default_v2') :
        get_template_part('partials/services/content', 'default-v2'); 

      elseif ($source_template == 'default') : 
        get_template_part('partials/content', 'default'); 

      elseif ($source_template == 'tax_detail') : 
        get_template_part('partials/services/content', 'tax-detail'); 

      elseif ($source_template == 'topic_page') : 
        get_template_part('partials/services/content', 'topic-page'); 

      elseif ($source_template == 'start_process') : 
        get_template_part('partials/services/content', 'start-process'); 
        get_template_part('partials/content', 'default'); 
    
      elseif ($source_template == 'vue_app') : 
        get_template_part('partials/services/content', 'vue-app'); 

      endif; 
    endwhile; 
  endif; 
  wp_reset_query(); 
endif; 
  ?> <!-- END Service Stub -->