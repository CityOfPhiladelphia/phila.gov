<?php
/*
  *
  * template for displaying L3 topic pages.
  *
  */
  ?>
<nav class="topics-nav small-24 large-8 columns mbm">
 <?php
   $current_term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
   $parent = get_term($current_term->parent, get_query_var('taxonomy') );
   echo '<a class="h3" href="' . get_term_link( $parent, $parent->taxonomy )  .'"><i class="fa fa-arrow-left"></i> ' . $parent->name . '</a>'; ?>
  <?php
    /* located in functions.php */
    phila_get_parent_topics(); ?>
</nav>

<div class="small-24 large-16 columns child results mbm">

<?php
  echo '<h1 class="h2">' . $current_term->name . '</h1>';

  $current_term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
  $parent_args = array(
    'post_parent' => 0,
    'post_type' => array(
      'page', 'service_post'
    ),
    'order' => 'asc',
    'orderby'=> 'menu_order',
    'tax_query' => array(
        array(
          'taxonomy' => 'topics',
          'field'    => 'slug',
          'terms'    => $current_term,
        ),
      ),
  );
  $parent_pages_query = new WP_Query( $parent_args );
  if ( $parent_pages_query->have_posts() ) :
    while ( $parent_pages_query->have_posts() ) : $parent_pages_query->the_post(); ?>
      <div class="row">
        <div class="small-24 columns">
          <ul>
            <li>
            <?php the_title( sprintf( '<a href="%s" class="item">', esc_url( get_permalink() ) ), '</a>' ); ?>
          </li>
        </ul>
        </div>
      </div>
      <?php
    endwhile;
    wp_reset_postdata();
     else :
       get_template_part( 'partials/content', 'none' );
     endif;
  ?>
</div>
