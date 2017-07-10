<?php
/**
 * The template used for displaying department websites
 *
 * @package phila-gov
 */

global $post;

$children = get_posts( array(
  'post_parent' => $post->ID,
  'orderby' => 'menu_order',
  'order' => 'ASC',
  'post_type' => 'department_page',
  'post_status' => 'publish'
));

$ancestors = get_post_ancestors($post);

//if there are grandchildren, don't redirect those.
if ( $children && count( $ancestors ) == 1 ) {
  $firstchild = $children[0];
  wp_redirect(get_permalink($firstchild->ID));
  exit;
}

$user_selected_template = phila_get_selected_template();

get_header(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class('department clearfix'); ?>>
  <header class="entry-header">
  <?php
  //TODO: clean up menu rendering
    $parent = phila_util_get_furthest_ancestor($post);

    if ( phila_util_is_v2_template( $parent->ID ) ) : ?>
    <?php $bg_img = phila_get_hero_header_v2( $parent->ID ); ?>
    <?php $bg_img_mobile = phila_get_hero_header_v2( $parent->ID, true ); ?>
    <?php $photo_credit = rwmb_meta( 'phila_v2_photo_credit', $parent->ID ); ?>

      <?php //TODO: unify desktop and mobile headers ?>
      <div class="hero-content" style="background-image:url(<?php echo $bg_img?>) ">
      <?php if ($user_selected_template == 'homepage_v2') : ?>
        <img class="show-for-small-only" src="<?php echo $bg_img_mobile?>" alt="">
      <?php endif; ?>
        <div class="hero-wrap">
        <?php if (!empty($photo_credit) ): ?>
          <div class="photo-credit small-text">
            <span><i class="fa fa-camera" aria-hidden="true"></i> Photo by <?php echo $photo_credit ?></span>
          </div>
        <?php endif; ?>
          <div class="row expanded <?php echo ($user_selected_template === 'homepage_v2') ? 'pbs pbxxl-mu ptxl-mu' : 'pbl' ?>">
            <div class="medium-18 small-centered columns text-overlay">
              <?php echo phila_get_department_homepage_typography( $parent ); ?>
              <?php if ($user_selected_template === 'homepage_v2'): ?>
                <div class="row">
                  <div class="medium-16 small-centered columns text-overlay">
                    <p class="sub-title mbn-mu"><strong><?php echo phila_get_item_meta_desc( ); ?></strong></p>
                  </div>
                </div>
              <?php endif;?>
            </div>
          </div>
            <div class="show-for-medium">
            <?php
              /*
              Our navigation menu. We use categories to drive functionality.
              This checks to make sure a category exists for the given page,
              if it does, we render our menu w/ markup.
              */
                phila_get_department_menu();
            ?>
          </div>
        </div>
      </div>
    </header>
    <div class="show-for-small-only">
    <?php
      /*
      Our navigation menu. We use categories to drive functionality.
      This checks to make sure a category exists for the given page,
      if it does, we render our menu w/ markup.
      */
        phila_get_department_menu();
    ?>
  </div>
    <?php else: ?>
      <div class="row">
        <div class="columns">
          <h1 class="entry-title contrast mbn"><?php echo $parent->post_title;?></h1>
        </div>
      </div>
  </header>
    <?php
    //get department homepage alerts
    call_user_func( array( 'Phila_Gov_Department_Sites', 'department_homepage_alert' ) );

    if ( $user_selected_template != 'off_site_department' ){
      /*
      Our navigation menu. We use categories to drive functionality.
      This checks to make sure a category exists for the given page,
      if it does, we render our menu w/ markup.
      */
        phila_get_department_menu();
    }
    ?>
  <?php endif; ?>

  <?php
    if ( $user_selected_template === 'off_site_department' ){

      get_template_part( 'templates/single', 'off-site' );

     }else{

      while ( have_posts() ) : the_post();
        get_template_part( 'templates/single', 'on-site-content' );
      endwhile;
    }
  ?>
</article><!-- #post-## -->
<?php get_footer(); ?>
