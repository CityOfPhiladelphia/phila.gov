<?php
/**
 * Standard template for a row of announcements
 *
 * @package phila-gov
 */
?>
<?php

  $categories = get_the_category();
  if( !phila_util_is_array_empty($categories) ) {
    $slang_name = phila_get_owner_typography( $categories[0] );
  }

  if ( !empty( $ann_cat_override )) :
    if( is_object($ann_cat_override[0])) {
      $slang_name = phila_get_owner_typography( $ann_cat_override[0] );
    }
    elseif( is_string($ann_cat_override[0])) {
      $cat_cleaned = phila_cat_id_to_cat_name($ann_cat_override[0]);
      $slang_name = phila_get_owner_typography( $cat_cleaned );
    }

  endif;

  if ( empty($ann_tag_override) ) :
    $ann_tag_override = '';
  elseif( is_array( $ann_tag_override) ):
    $ann_tag_override = implode(',', $ann_tag_override);
  elseif (!empty($ann_tag_override)):
    $ann_tag_override = $ann_tag_override;
  endif;
  ?>

<section class="row">
  <?php 
    global $post;
    $a = array(
      'name' => 'Announcements',
      'tag' => $ann_tag_override,
    );
  
    if (isset($ann_cat_override[0])){
      $category = array($ann_cat_override[0]);
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
    
    include( locate_template( 'partials/posts/announcements-grid.php' ) );
  
    wp_reset_postdata();
  ?>
</section>
