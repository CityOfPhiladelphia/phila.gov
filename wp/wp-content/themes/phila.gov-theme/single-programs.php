<?php
/**
 * The template used for displaying program sites
 *
 * @package phila-gov
 */

get_header();

$user_selected_template = phila_get_selected_template();

?>

<div id="post-<?php the_ID(); ?>" <?php post_class('program clearfix'); ?>>
  <header>

  <?php

    $parent = phila_util_get_furthest_ancestor($post);

    /**
     * Department Homepage V2 Hero
     */
    $hero_data = array(
      'parent' => phila_util_get_furthest_ancestor($post),
      'is_homepage_v2' => $user_selected_template == 'homepage_v2',
      'bg' => array(
        'desktop'      => phila_get_hero_header_v2( $parent->ID ),
        'mobile'       => phila_get_hero_header_v2( $parent->ID, true ),
        'photo_credit' => rwmb_meta( 'phila_v2_photo_credit', $parent->ID )
      )
    );

  //    get_dept_partial('hero', $hero_data);


      while ( have_posts() ) : the_post();
      get_template_part( 'partials/departments/v2/our', 'services' );
      get_template_part( 'partials/departments/content', 'programs-initiatives' );
      endwhile;

  ?>
</div><!-- #post-## -->
<?php get_footer(); ?>
