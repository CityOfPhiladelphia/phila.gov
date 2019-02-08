<?php
/**
 * The template used for displaying Staff Directory
 *
 * @package phila-gov
 */
?>
<?php
global $post;

$user_selected_template = phila_get_selected_template();
$category_override = rwmb_meta('phila_get_staff_cats');
$unit_data = get_post_meta( $post->ID, 'units' );
$all_staff = rwmb_meta('full_list');
$unit_count = -1;
$anchor_list = rwmb_meta('anchor_list');
?>

<?php if ( !empty($unit_data) && $anchor_list == 1 )  : ?>
<div class="row mbl">
  <div class="columns">
    <h3>On this page</h3>
    <nav>
    <ul class="no-bullet">
    <?php foreach ($unit_data as $unit): ?>
      <li class="pas"><a class="" href="#<?php echo $unit ?>"><?php echo urldecode($unit) ?></a></li>
    <?php endforeach; ?>
    </ul>
    </nav>
  </div>
</div>
<?php endif; ?>
<?php 

if ( has_category() ) {
  $categories = get_the_category();
  $category_id = $categories[0]->cat_ID;

  if ( !empty( $category_override ) ) {
    $category_id = implode(", ", $category_override['phila_staff_category']);
  }

  if (!empty( $unit_data ) ) {

    $unit_meta = rwmb_meta( 'department_units', array( 'object_type' => 'term' ), $category_id );

    foreach ( $unit_data as $unit ){
      /* The Staff Directory Loop, when there are units */
      $args = array(
        'orderby' => 'title',
        'order' => 'ASC',
        'post_type' => 'staff_directory',
        'cat' => array($category_id),
        'posts_per_page' => -1,
        'meta_key' => 'units',
        'meta_value' => $unit,
      );
      
      include(locate_template('partials/departments/phila_staff_directory_loop.php'));

    }

    /* Staff within units have been displayed, don't show them now */
    $args = array(
      'orderby' => 'title',
      'order' => 'ASC',
      'post_type' => 'staff_directory',
      'cat' => array($category_id),
      'posts_per_page' => -1,
      'meta_query' => array(
        'relation' => 'AND',
        array(
          'key'     => 'units',
          'value'   => '',
          'compare' => 'NOT EXISTS'
          )
        )
      );

      $unit = null;
      include(locate_template('partials/departments/phila_staff_directory_loop.php'));

    }else{
      $hidden = rwmb_meta('hide_units');

      if ( !empty( $hidden ) ) {
        $args = array(
          'orderby' => 'title',
          'order' => 'ASC',
          'post_type' => 'staff_directory',
          'cat' => array($category_id),
          'posts_per_page' => -1,
          'meta_query' => array(
            'relation' => 'AND',
            array(
              'key'     => 'units',
              'value'   => '',
              'compare' => 'NOT EXISTS'
            )
          )
        );

        $unit = null;
        include(locate_template('partials/departments/phila_staff_directory_loop.php'));

      } else {

        /* There are no units, display normally */
        $args = array(
          'orderby' => 'title',
          'order' => 'ASC',
          'post_type' => 'staff_directory',
          'cat' => array($category_id),
          'posts_per_page' => -1,
          );

        include(locate_template('partials/departments/phila_staff_directory_loop.php'));
      }
    }

  }?>

<?php if (phila_get_selected_template() != 'homepage_v2') : ?>
  <?php get_template_part( 'partials/departments/v2/board_commission_member_list' ); ?>
<?php endif?>
