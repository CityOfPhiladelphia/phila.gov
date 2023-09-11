<?php
/**
 * The template used for displaying program sites
 *
 * @package phila-gov
*/

$user_selected_template = phila_get_selected_template();
$language = rwmb_meta('phila_select_language');
$language_list = phila_get_translated_language( $language );
get_header();
?>
<div id="post-<?php the_ID(); ?>" <?php post_class('program clearfix'); ?>>

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
      include( locate_template( 'partials/programs/header.php' ) );

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
          get_template_part( 'partials/departments/v2/homepage_timeline' );
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
