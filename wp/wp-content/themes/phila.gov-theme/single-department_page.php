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
  'post_status' => 'any'
));

$ancestors = get_post_ancestors($post);

//if there are grandchildren, don't redirect those.
if ( $children && count( $ancestors ) == 1 ) {
  $firstchild = $children[0];
  wp_redirect(get_permalink($firstchild->ID));
  exit;
}
get_header(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class('department clearfix'); ?>>
  <div class="row">
    <header class="entry-header small-24 columns">
      <?php
       /* Get an array of Ancestors and Parents if they exist */
        $parents = get_post_ancestors( $post->ID );
        /* Get the top Level page->ID count base 1, array base 0 so -1 */
        $id = ($parents) ? $parents[count($parents)-1]: $post->ID;
        if ($id === $post->ID) {

        }
        $parent = get_post( $id );
        ?>
        <h1 class="entry-title contrast mbn"><?php echo $parent->post_title;?></h1>
    </header>
  </div>
    <?php
  //get department homepage alerts
  call_user_func( array( 'Phila_Gov_Department_Sites', 'department_homepage_alert' ) );
  ?>
  <?php
    /*
    Our navigation menu. We use categories to drive functionality.
    This checks to make sure a category exists for the given page,
    if it does, we render our menu w/ markup.
    */
      phila_get_department_menu();
  ?>
  <?php
  if (function_exists('rwmb_meta')) {
    $external_site = rwmb_meta( 'phila_dept_url', $args = array('type' => 'url'));
    if (!$external_site == ''){

      get_template_part( 'templates/single', 'off-site' );

     } else {
       //loop for our regularly scheduled content
       while ( have_posts() ) : the_post();

        get_template_part( 'templates/single', 'on-site-content' );

        endwhile;
      }
  }
  ?>
</article><!-- #post-## -->
<?php get_footer(); ?>
