<?php
/*
  *
  * template for displaying 2nd level topics page.
  *
  */
  ?>
<nav class="topics-nav small-24 large-8 columns">
 <?php
   $current_term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
   $parent = get_term($current_term->parent, get_query_var('taxonomy') );
   echo '<h3 class="back-arrow"><a href="' . get_term_link( $parent, $parent->taxonomy )  .'">' . $parent->name . '</a></h3>'; ?>
  <?php get_parent_topics(); ?>
</nav>

<div class="small-24 large-16 columns child results">

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

  			<?php get_template_part( 'partials/content', 'list' ); ?>
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
