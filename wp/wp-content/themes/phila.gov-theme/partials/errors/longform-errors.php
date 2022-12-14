<?php 
if ( is_user_logged_in() ) {
  $error_message_title = 'Warning: the following sections are not displaying. This template only supports sections up to seven levels deep. Move this content into a section that will display.';
  $error_messages = [];

  $args = array(
    'post_type'      => 'longform_content',
    'posts_per_page' => -1,
    'post_parent'    => get_the_ID(),
    'order'          => 'ASC',
    'orderby'        => 'menu_order'
  );


  $parent = new WP_Query( $args );

  if ( $parent->have_posts() ) {

    while ( $parent->have_posts() ) : $parent->the_post(); 
      $args1 = array(
        'post_type'      => 'longform_content',
        'posts_per_page' => -1,
        'post_parent'    => get_the_ID(),
        'order'          => 'ASC',
        'orderby'        => 'menu_order'
      );
      
      $parent1 = new WP_Query( $args1 );
      if ( $parent1->have_posts() ) {

        while ( $parent1->have_posts() ) : $parent1->the_post(); 
          $args2 = array(
            'post_type'      => 'longform_content',
            'posts_per_page' => -1,
            'post_parent'    => get_the_ID(),
            'order'          => 'ASC',
            'orderby'        => 'menu_order'
          );
          
          $parent2 = new WP_Query( $args2 );
          if ( $parent2->have_posts() ) {
      
            while ( $parent2->have_posts() ) : $parent2->the_post(); 
              $args3 = array(
                'post_type'      => 'longform_content',
                'posts_per_page' => -1,
                'post_parent'    => get_the_ID(),
                'order'          => 'ASC',
                'orderby'        => 'menu_order'
              );
              
              $parent3 = new WP_Query( $args3 );
              if ( $parent3->have_posts() ) {
          
                while ( $parent3->have_posts() ) : $parent3->the_post(); 
                  $args4 = array(
                    'post_type'      => 'longform_content',
                    'posts_per_page' => -1,
                    'post_parent'    => get_the_ID(),
                    'order'          => 'ASC',
                    'orderby'        => 'menu_order'
                  );
                  
                  $parent4 = new WP_Query( $args4 );
                  if ( $parent4->have_posts() ) {
              
                    while ( $parent4->have_posts() ) : $parent4->the_post(); 
                      $args5 = array(
                        'post_type'      => 'longform_content',
                        'posts_per_page' => -1,
                        'post_parent'    => get_the_ID(),
                        'order'          => 'ASC',
                        'orderby'        => 'menu_order'
                      );
                      
                      $parent5 = new WP_Query( $args5 );
                      if ( $parent5->have_posts() ) {
                  
                        while ( $parent5->have_posts() ) : $parent5->the_post(); 
                          $args6 = array(
                            'post_type'      => 'longform_content',
                            'posts_per_page' => -1,
                            'post_parent'    => get_the_ID(),
                            'order'          => 'ASC',
                            'orderby'        => 'menu_order'
                          );
                          
                          $parent6 = new WP_Query( $args6 );
                          if ( $parent6->have_posts() ) {
                      
                            while ( $parent6->have_posts() ) : $parent6->the_post(); 
                              $args7 = array(
                                'post_type'      => 'longform_content',
                                'posts_per_page' => -1,
                                'post_parent'    => get_the_ID(),
                                'order'          => 'ASC',
                                'orderby'        => 'menu_order'
                              );
                              
                              $parent7 = new WP_Query( $args7 );
                              if ( $parent7->have_posts() ) {
                          
                                while ( $parent7->have_posts() ) : $parent7->the_post(); 
                                  $item['link'] = get_edit_post_link();
                                  $item['text'] = get_the_title();
                                  array_push($error_messages, $item);
                                  $args8 = array(
                                    'post_type'      => 'longform_content',
                                    'posts_per_page' => -1,
                                    'post_parent'    => get_the_ID(),
                                    'order'          => 'ASC',
                                    'orderby'        => 'menu_order'
                                  );
                                  
                                  $parent8 = new WP_Query( $args8 );
                                  if ( $parent8->have_posts() ) {
                              
                                    while ( $parent8->have_posts() ) : $parent8->the_post(); 
                                      $item['link'] = get_edit_post_link();
                                      $item['text'] = get_the_title();
                                      array_push($error_messages, $item);
                                    endwhile;
                                  }
                                endwhile;
                              }
                            endwhile;
                          }
                        endwhile;
                      }
                    endwhile;
                  }
                endwhile;
              }
            endwhile;
          }
        endwhile;
      }
    endwhile;
  }
  wp_reset_postdata();
}