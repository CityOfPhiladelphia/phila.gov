<?php
/*
 * Partial for rendering all programs in this department's category.
 *
 */
?>
<?php
/**
 * See all programs template
 * @version 0.23.0
 * @package phila-gov
 */

get_header(); ?>

<div class="row">
  <div class="columns results a-z-list border-top-none">
  <?php
  $args = array(
    'post_type'  => 'programs',
    'posts_per_page'  => -1,
    'order' => 'ASC',
    'post_parent' => 0,
    'orderby' => 'title',
    'category__in' => phila_util_cat_ids(),
  );
  $program_pages = new WP_Query( $args ); ?>

  <?php if ( $program_pages->have_posts() ) : ?>

    <?php
      $a_z = range('a','z');
      $a_z = array_fill_keys($a_z, false);
      $service_title = array();
      $service_desc = array();
      $service_link = array();
      $service_parent = array();
    ?>

    <?php while ( $program_pages->have_posts() ) : $program_pages->the_post(); ?>
        <?php
          //overwrite range array with values that exist
          $a_z[strtolower(substr($post->post_title, 0, 1 ))] = true; ?>

        <?php

        $desc['desc'] = phila_get_item_meta_desc( $blog_info = false );
        $link['link'] = get_permalink();

        $service_desc[$post->post_title] = $desc;

        $service_link[$post->post_title] = $link;

        $services = array_merge_recursive($service_title, $service_desc, $service_link, $service_parent);

        ?>
      <?php endwhile; ?>
    <?php foreach($a_z as $a_k => $a_v): ?>
      <?php if( $a_v == true): ?>
        <div class="row collapse a-z-group" data-alphabet=<?php echo $a_k ?>>
            <div class="small-3 medium-2 columns">
              <span class="letter h1" id="<?php echo $a_k ?>"><?php echo strtoupper($a_k); ?></span>
            </div>
            <div class="small-20 medium-21 columns">
        <?php endif; ?>
        <?php foreach($services as $k => $v) :?>
          <?php
            $first_c = strtolower($k[0]);
            if( $a_k == $first_c && $a_v == true ) : ?>
              <div class="result mvm">
                <a href="<?php echo $v['link']?>"><?php echo $k ?><?php echo isset( $v['parent'] ) ? ' - ' . get_the_title ($v['parent']) : '' ?></a>
                <p class="hide-for-small-only mbl"><?php echo $v['desc'] ?></p>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
          <?php if( $a_v == true): ?>
          </div>
        </div>
        <?php endif; ?>

      <?php endforeach; ?>
    <?php endif; ?>
    <?php wp_reset_query(); ?>
  </div>
</div> <!-- .row -->

<?php get_footer(); ?>
