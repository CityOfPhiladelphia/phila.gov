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
       /* Get an array of Ancestors and Parents if they exist */
        $parents = get_post_ancestors( $post->ID );
        /* Get the top Level page->ID count base 1, array base 0 so -1 */
        $id = ($parents) ? $parents[count($parents)-1]: $post->ID;
        if ($id === $post->ID) {

        }
        $parent = get_post( $id );

        if (strpos($user_selected_template, '_v2') !== false) : ?>

          <div class="hero-content">
            <?php // TODO: Get the actual hero image and call it in the img src ?>
            <img class="show-for-small-only" src="https://ec2-54-165-91-192.compute-1.amazonaws.com/wp-content/themes/phila.gov-theme/img/beta_homepage_coverphoto_full.jpg" alt="">
            <div class="hero-wrap">
            <?php // TODO: Determine whether or not we need photo credits ?>
              <!-- <div class="photo-credit small-text">
                <span><i class="fa fa-camera" aria-hidden="true"></i> Photo by M. Edlow for Visit Philadelphia</span>
              </div> -->
              <?php // TODO: Get the actual hero image and apply as background via inline css ?>
              <div class="row expanded ptl pbs pvxl-mu">
                <div class="medium-14 small-centered columns beta-message">

                  <?php
                  // TODO: Call title with $parent->post_title, regex match on 'Department of', 'Office of' to add line break. This can be refactored and should become a util if we intend to reuse

                  $target_phrases = array("Department of","Office of");
                  $break_after_phrases = array('<span class="h4 break-after" style="line-height:1;">Department of</span>','<span class="h4 break-after" style="line-height:1;">Office of</span>');
                  $new_title = str_replace($target_phrases,$break_after_phrases, $parent->post_title );
                  echo  '<h1 style="line-height:1">' . $new_title . '</h1>';
                  ?>
                  <p class="mvs mbn-mu"><em><?php echo phila_get_item_meta_desc(); ?></em></p>
                </div>
              </div>
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
        </header>
      </div>
        <?php else: ?>
          <h1 class="entry-title contrast mbn"><?php echo $parent->post_title;?></h1>
      </header>
    <?php
    //get department homepage alerts
    call_user_func( array( 'Phila_Gov_Department_Sites', 'department_homepage_alert' ) );

    /*
    Our navigation menu. We use categories to drive functionality.
    This checks to make sure a category exists for the given page,
    if it does, we render our menu w/ markup.
    */
      phila_get_department_menu();
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
