<?php
/**
 * Template Name: The latest - Events archive
 * Description: Custom Page template for Events archive
 * @package phila-gov
 */

get_header(); ?>

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
if ( $calendar_q->have_posts() ) : ?>
<?php $post_ids = array();?>
<?php $cal_ids = array(); ?>
<?php $cal_cats = array();?>
  <?php while ( $calendar_q->have_posts() ) : $calendar_q->the_post(); ?>
    <?php $categories = get_the_category( get_the_id() ); ?>
    <?php if ($categories != null) : ?>
      <?php array_push($post_ids, get_the_id() ); ?>
      <?php array_push($cal_cats, $categories[0]->cat_ID);?>
    <?php endif;?>
  <?php endwhile; ?>
  <?php wp_reset_postdata(); ?>
<?php endif; ?>
<?php foreach ($post_ids as $post_id) : ?>

  <?php array_push($cal_ids, base64_decode(get_post_meta( $post_id, '_google_calendar_id', true ) ) ); ?>
<?php endforeach; ?>
  <?php $final_array = array_combine($cal_cats, $cal_ids); ?>
<?php $calendar_ids = json_encode($final_array);
  var_dump($calendar_ids);
  /* g_cal_data - array of all calendar ids, duplicates removed */
  wp_localize_script('vuejs-app',
  'g_cal_data', array(
    'json' => __($calendar_ids)
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
<?php get_footer(); ?>
