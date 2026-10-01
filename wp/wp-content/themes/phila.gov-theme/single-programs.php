<?php
/**
 * The template used for displaying program sites
 *
 * @package phila-gov
*/
global $post;


/**
 * Get a department specific partial template with scoped args
 * @param  string $partial_name name of partial after hyphen
 * @param  array  $partial_args arguments to scope to that specifc partial
 */
function get_dept_partial($partial_name, $partial_args = array()){
  global $post;
  phila_get_template_part('partials/departments/v2/department-'.$partial_name, $partial_args);
}

$content = $post->post_content;
$children = get_posts( array(
  'post_parent' => $post->ID,
  'orderby'     => 'menu_order',
  'order'       => 'ASC',
  'post_type'   => 'department_page',
  'post_status' => 'publish'
));

$ancestors = get_post_ancestors($post);
$parent = wp_get_post_parent_id($post);

$user_selected_template = phila_get_selected_template();

$parent_template = phila_get_selected_template($parent);

$language = rwmb_meta('phila_select_language');
$language_list = phila_get_translated_language( $language );
get_header();
?>
<div id="post-<?php the_ID(); ?>" <?php post_class('program clearfix'); ?>>

  <?php

    $parent = phila_util_get_furthest_ancestor($post);
    if ( $user_selected_template == 'prog_homepage_v2' ) :
      /**
       * Department Homepage V2 Hero
       */
      $hero_data = array(
        'parent' => phila_util_get_furthest_ancestor($post),
        'is_homepage' => ($user_selected_template == 'prog_homepage_v2'),
        'bg' => array(
          'desktop'      => phila_get_hero_header_v2( $parent->ID ),
          'mobile'       => phila_get_hero_header_v2( $parent->ID, true ),
          'photo_credit' => rwmb_meta( 'phila_v2_photo_credit', $parent->ID )
        )
      );

      get_dept_partial('hero', $hero_data);
    endif;
  ?>

<?php if ( $user_selected_template == 'prog_off_site' ) : ?>

  <?php include(locate_template('templates/single-off-site.php')); ?>

  <?php get_footer(); ?>
  <?php return; ?>
<?php endif;?>

<?php if ($user_selected_template == 'stub'): ?>
  <?php include( locate_template( 'partials/programs/header.php' ) ); ?>

<?php
  include(locate_template('partials/programs/stub.php'));
  get_footer();

  return; ?>
  <?php endif;?>

<?php if ($user_selected_template == 'translated_content'): ?>
  <?php include( locate_template( 'partials/programs/header.php' ) ); ?>

<?php
  include(locate_template ('partials/posts/post-translated-content.php') );
  include(locate_template('partials/global/translated-content.php'));
  get_footer();

  return; ?>
<?php endif;?>

<?php if ($user_selected_template == 'covid_guidance'): ?>
  <?php include( locate_template( 'partials/programs/header.php' ) ); ?>

<?php
  include(locate_template('partials/programs/covid-guidance.php'));
  get_footer();

  return; ?>
<?php endif;?>


  <?php
    while ( have_posts() ) : the_post();
      if ( $user_selected_template !== 'prog_homepage_v2' ) :
        include( locate_template( 'partials/programs/header.php' ) );
      endif;

      if ( count( $language_list ) >= 2 ):
        include(locate_template ('partials/posts/post-translated-content.php') );
      endif;

      get_template_part( 'partials/content', 'custom-markup-before-wysiwyg' ); ?>
      <?php if( !empty( get_the_content() ) ) : ?>
        <?php include( locate_template( 'partials/content-basic.php' ) ); ?>
      <?php endif; ?>

      <?php get_template_part( 'partials/content', 'custom-markup-after-wysiwyg' ); ?>

      <?php get_template_part( 'partials/departments/v2/our', 'services' );?>

      <?php
      switch ($user_selected_template){
        case ('phila_one_quarter'):
          get_template_part( 'partials/departments/v2/content', 'one-quarter' );
          break;
        case ('resource_list_v2'):
          include(locate_template('partials/resource-list.php'));
          break;
        case('collection_page_v2') :
          include(locate_template('partials/departments/v2/collection-page.php'));
          break;
        case('document_finder_v2'):
          include(locate_template('partials/departments/v2/document-finder.php'));
          break;
        case 'timeline':
          get_template_part( 'partials/timeline_stub' );
          break;
        case ('child_index'):
          get_template_part( 'partials/departments/v2/child', 'index' );
          break;
      } ?>
      <?php include( locate_template( 'partials/content-phila-row.php' ) );  ?>

      <?php get_template_part( 'partials/content', 'additional' ); ?>

    <?php endwhile; ?>
</div><!-- #post-## -->

<?php include(locate_template('partials/global/on-load-modal.php')); ?>

<?php get_footer(); ?>