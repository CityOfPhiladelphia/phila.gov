<?php
/**
 * Standard template for a full row of 3 blog posts
 *
 * @package phila-gov
 */
?>
<?php

  $categories = get_the_category();
  if( !phila_util_is_array_empty($categories) ) {
    $slang_name = phila_get_owner_typography( $categories[0] );
  }

  if ( empty($blog_see_all) ) : 
    $blog_see_all = '';
  endif;

  if ( !empty( $blog_cat_override )) :
    if( is_object($blog_cat_override[0])) {
      $slang_name = phila_get_owner_typography( $blog_cat_override[0] );
    }
    elseif( is_string($blog_cat_override[0])) {
      $cat_cleaned = phila_cat_id_to_cat_name($blog_cat_override[0]);
      $slang_name = phila_get_owner_typography( $cat_cleaned );
    }

  endif;

  if ( empty($blog_tag_override) ) :
    $blog_tag_override = '';
  elseif( is_array( $blog_tag_override) ):
    $blog_tag_override = implode(',', $blog_tag_override);
  elseif (!empty($blog_tag_override)):
    $blog_tag_override = $blog_tag_override;
  endif;
  ?>

<section class="row">
  <?php 
    global $post;
    $a = array(
      'name' => 'Posts',
      'tag' => $blog_tag_override,
      'see_all' => $blog_see_all
    );
  
    if (isset($blog_cat_override[0])){
      $category = array($blog_cat_override[0]);
    } else {
      $category = array();
      $cats = get_the_category();
      foreach ($cats as $cat) {
        array_push($category, $cat->term_id);
      }
    }
  
    if ( !empty($a['tag'] ) ){
      $tag = explode(',', $a['tag']);
    }
  
    if ( !empty($a['see_all']) ){
      $override_url = $a['see_all'];
    }
    
    include( locate_template( 'partials/posts/post-grid.php' ) );
  
    wp_reset_postdata();
  ?>
</section>
