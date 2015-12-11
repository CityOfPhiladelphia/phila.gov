<?php

/*
 *
 *  Related Topics Sidebar
 *
 */

 ?>
<aside id="secondary" class="widget-area small-24 medium-6 columns" role="complementary">
  <div class="related">
      <?php
     //get all the terms
        $custom_terms = get_the_terms($post->ID, 'topics');
        $currentID = get_the_ID();

        if($custom_terms){
            // loop through topics and build a tax query
            foreach( $custom_terms as $custom_term ) {
                $terms[] = $custom_term->slug;
            }

            $tax_query = array('relation' => 'OR',
                    array (
                        'taxonomy' => 'topics',
                        'field' => 'slug',
                        'terms' => $terms
                    )
            );
            $args = array( 'post_type' => array(
                                'post',
                                'service_post'
                                ),
                            'orderby'=>'name',
                            'order' => 'ASC',
                            'posts_per_page' => 5,
                            'tax_query' => $tax_query,
                            'post__not_in' => array($currentID),

            );
            $loop = new WP_Query($args);

            if( $loop->have_posts() ) {
               ?> <h3><?php printf( __( 'Related Content', 'phila-gov' )); ?></h3>
                <ul>
                    <?php

                while( $loop->have_posts() ) : $loop->the_post(); ?>
                    <li>
                        <?php the_title( sprintf( '<a href="%s" rel="bookmark" class="item">', esc_url( get_permalink() ) ), '</a>' );
                    ?>
                    </li>
                <?php

                endwhile;
              ?></ul> <?php
            }

            wp_reset_query();

        }
    ?>
  </div><!-- .related -->
</aside><!-- #secondary -->
