<?php
/**
 * The template used for displaying program sites
 *
 * @package phila-gov
*/

$user_selected_template = phila_get_selected_template();

get_header();

?>

<?php if ( $user_selected_template == 'prog_off_site' ) : ?>

  <?php include(locate_template('templates/single-off-site.php')); ?>

  <?php get_footer(); ?>
  <?php return; ?>
<?php endif;?>

<?php if ($user_selected_template == 'programs') :  ?>
  <div id="post-<?php the_ID(); ?>" <?php post_class('program clearfix'); ?>

  <?php include( locate_template( 'partials/programs/header.php' ) ); ?>
      <div class="row">
      <div class="columns">
        <div class="one-quarter-layout">

          <?php
          $args = array(
            'post_parent' => $post->ID,
            'post_type'   => 'any',
            'numberposts' => -1,
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'post_status' => array('publish', 'private')
          );
          $children = get_children( $args );

          $last_key = phila_util_is_last_in_array( (array) $children );

          foreach ($children as $key => $child) : ?>

          <div class="row one-quarter-row mvl">
            <div class="medium-6 columns">
              <h3 id="<?php echo sanitize_title_with_dashes($child->post_title)?>"><?php echo $child->post_title ?></h3>
            </div>
              <div class="medium-18 columns pbxl">
                <?php echo rwmb_meta('phila_meta_desc', '', $child->ID)?>
                <a href="<?php echo get_permalink($child->ID) ?>">Learn more <i class="fas fa-arrow-right"></i></a>
              </div>
            </div>
            <?php if ($last_key != $key) : ?>
            <hr class="mhn"/>
          <?php endif ?>
          <?php endforeach;?>
        </div>
      </div>
    </div>
  <?php get_footer(); ?>
<?php return; ?>
<?php endif;?>


<div id="post-<?php the_ID(); ?>" <?php post_class('program clearfix'); ?>>
  <?php
    while ( have_posts() ) : the_post();
      include( locate_template( 'partials/programs/header.php' ) );

      get_template_part( 'partials/content', 'custom-markup-before-wysiwyg' ); ?>
      <?php if( !empty( get_the_content() ) ) : ?>
        <div class="row">
          <div class="columns">
            <?php the_content(); ?>
          </div>
        </div>
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

      } ?>
      <?php get_template_part( 'partials/departments/content', 'programs-initiatives' ); ?>

      <?php get_template_part( 'partials/content', 'additional' ); ?>

    <?php endwhile; ?>
</div><!-- #post-## -->
<?php get_footer(); ?>
