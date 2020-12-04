<?php
/**
 * Standard template for a full row of 3 blog posts
 *
 * @package phila-gov
 */
?>
<?php

//   $categories = get_the_category();
//   if( !phila_util_is_array_empty($categories) ) {
//     $category_id = $categories[0]->cat_ID;
//     $slang_name = phila_get_department_homepage_typography( null, $return_stripped = true, $page_title = $categories[0]->name );
//   }

//   if ( empty($blog_see_all) ) : 
//     $blog_see_all = '';
//   endif;

//   if ( !empty( $blog_cat_override )) :
//     if( is_object($blog_cat_override[0])) {
//       $slang_name = phila_get_department_homepage_typography( null, $return_stripped = true, $page_title = $blog_cat_override[0]->name );
//     }
//     elseif( is_string($blog_cat_override[0])) {
//       $cat_cleaned = phila_cat_id_to_cat_name($blog_cat_override[0]);
//       $slang_name = phila_get_department_homepage_typography( null, $return_stripped = true, $page_title = $cat_cleaned->name );
//     }

//   endif;

//   if ( empty($blog_tag_override) ) :
//     $blog_tag_override = '';
//   elseif( is_array( $blog_tag_override) ):
//     $blog_tag_override = implode(',', $blog_tag_override);
//   elseif (!empty($blog_tag_override)):
//     $blog_tag_override = $blog_tag_override;
//   endif;
//   ?>

// <section class="row">
//   <?php 
//     global $post;
//     $a = array(
//       'name' => 'Posts',
//       'tag' => $blog_tag_override,
//       'see_all' => $blog_see_all
//     );
  
//     if (isset($blog_cat_override[0])){
//       $category = array($blog_cat_override[0]);
//     } else {
//       $category = array();
//       $cats = get_the_category();
//       foreach ($cats as $cat) {
//         array_push($category, $cat->term_id);
//       }
//     }
  
//     if ( !empty($a['tag'] ) ){
//       $tag = explode(',', $a['tag']);
//     }
  
//     if ( !empty($a['see_all']) ){
//       $override_url = $a['see_all'];
//     }
    
//     include( locate_template( 'partials/posts/announcements-grid.php' ) );
//     include( locate_template( 'partials/posts/post-grid.php' ) );
  
//     wp_reset_postdata();
  ?>
</section>
