<?php
/**
 * Standard template for a full row of 3 blog posts
 *
 * @package phila-gov
 */
?>
<?php

  $categories = get_the_category();
  $category_id = $categories[0]->cat_ID;

  if ( !empty( $blog_cat_override ) ) :
    $category_id = $blog_cat_override[0];
  endif;

  if ( empty($blog_tag_override)) :
    $blog_tag_override = '';
  endif;
  ?>
<section class="row">
  <?php echo do_shortcode('[recent-posts posts="3" category="' . $category_id .'" tag="'. $blog_tag_override .'"]'); ?>
</section>
