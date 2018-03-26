<?php
/**
 * The template used for displaying department websites
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
$template = phila_get_selected_template();


//Don't redirect when: this is a grandchild, there is content in the wysiwyg or, if the default template is 'department_page'. department_page will always be the default if there is no other template selected.
if ( $children && count( $ancestors ) == 1  && empty( $content ) && $template == 'department_page' )  {
  $firstchild = $children[0];
  wp_redirect(get_permalink($firstchild->ID));
  exit;
}
$user_selected_template = phila_get_selected_template();

get_header(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class('department clearfix'); ?>>

  <?php

    $parent = phila_util_get_furthest_ancestor($post);

    if ( phila_util_is_v2_template( $parent->ID ) ) :

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

      get_dept_partial('hero', $hero_data);


    else: //it's an old-style department ?>
    <header>
      <div class="row">
        <div class="columns">
          <h1 class="entry-title contrast mbn"><?php echo $parent->post_title;?></h1>
        </div>
      </div>
    </header>
    <div class="menu-old">
      <?php
        //get department homepage alerts
        call_user_func( array( 'Phila_Gov_Department_Sites', 'department_homepage_alert' ) );

        if ( $user_selected_template != 'off_site_department' ){
          /*
          Our navigation menu. We use categories to drive functionality.
          This checks to make sure a category exists for the given page,
          if it does, we render our menu w/ markup.
          */
            phila_get_menu();
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
</div><!-- #post-## -->
<?php get_footer(); ?>
