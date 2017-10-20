<?php
/**
 * Standard template for a full row of 3 blog posts
 *
 * @package phila-gov
 */
?>
<?php
  if ( !isset($blog_cat_override) ) {
    $blog_cat_override = rwmb_meta('phila_get_post_cats');
  }
  $categories = get_the_category();
  $category_id = $categories[0]->cat_ID;

  if ( !empty( $blog_cat_override ) ) :
    $category_id = implode(", ", $blog_cat_override['phila_post_category']);
  endif;
  ?>
<section class="row">
  <?php echo do_shortcode('[recent-posts posts="3" category=" ' . $category_id .' "]'); ?>
</section>
