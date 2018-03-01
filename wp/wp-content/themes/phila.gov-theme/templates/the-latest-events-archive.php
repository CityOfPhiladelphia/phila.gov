<?php
/**
 * Template Name: The latest - Events archive
 * Description: Custom Page template for Events archive
 * @package phila-gov
 */

get_header();
?>
<div class="row">
  <header class="columns">
    <h1 class="contrast">
       <?php echo get_the_title(); ?>
    </h1>
  </header>
</div>

<?php
$cal_a = array(
  'post_type' => 'calendar',
  'posts_per_page'  => -1,
  'post_status' => 'any'
);
$calendar_q = new WP_Query( $cal_a );

if ( $calendar_q->have_posts() ) {
  $post_ids = array();
  $cal_ids = array();
  $cal_cat_ids = array();
  $cal_nice_name = array();
  $links = array();
  while ( $calendar_q->have_posts() ) : $calendar_q->the_post();
    $categories = get_the_category( get_the_id() );
    if ($categories != null) {
      array_push($post_ids, get_the_id() );
      array_push($cal_cat_ids, $categories[0]->cat_ID);
      array_push($cal_nice_name, $categories);
    }
  endwhile;

    wp_reset_postdata();
  }
  foreach ($post_ids as $post_id) {
    array_push($cal_ids, base64_decode(get_post_meta( $post_id, '_google_calendar_id', true ) ) );
  }
  foreach ($cal_nice_name as $nice){
    //var_dump($nice[0]);
    $links[$nice[0]->cat_ID] = phila_get_current_department_name($nice);
  }
  $final_array = array_combine($cal_cat_ids, $cal_ids);
  $final_array = array_filter($final_array);

  $links = array_filter($links);


  $calendar_ids = json_encode($final_array);

  //var_dump($links);
  //var_dump($final_array);

  /* g_cal_data - Object: key is category ID, value is ids*/
  wp_localize_script('vuejs-app',
  'g_cal_data', array(
    'json' => __($calendar_ids)
      )
    );
    /*calendar_owners - object: key is category ID, value is department link */
    wp_localize_script('vuejs-app',
    'calendar_owners', array(
      'json' => __($links)
        )
      );

?>
<section id="events-archive" class="content-area archive">

  <div class="row">
    <main id="main" class="site-main medium-20 columns medium-centered">

      <div id="all-events">
        <div class="center"><i class="fa fa-spinner fa-spin fa-3x"></i></div>
      </div>

    </main><!-- #main -->
  </div>
</section><!-- #primary -->

<?php get_footer();?>
