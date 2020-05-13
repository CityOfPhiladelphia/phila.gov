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
  $slang_name = phila_get_department_homepage_typography( null, $return_stripped = true, $page_title = $categories[0]->name );

  $category_slang_name = $categories[0]->slang_name = html_entity_decode(trim($slang_name));

  if ( empty($blog_see_all) ) : 
    $blog_see_all = '';
  endif;

  if ( !empty( $blog_cat_override ) && is_object($blog_cat_override[0])) :
    $slang_name = phila_get_department_homepage_typography( null, $return_stripped = true, $page_title = $blog_cat_override[0]->name );

  endif;

  if ( empty($blog_tag_override) ) :
    $blog_tag_override = '';
  elseif( is_array( $blog_tag_override) ):
    $blog_tag_override = implode(',', $blog_tag_override);
  endif;
  ?>
<section class="row">
  <?php echo do_shortcode('[recent-posts posts="3" see_all="' . $blog_see_all .'"  department="' . $slang_name .'" tag="'. $blog_tag_override .'"]'); ?>
</section>
