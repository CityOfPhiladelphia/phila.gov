<?php
/*
 * Partial for rendering all services in this department category.
 *
 */
?>
<?php
/**
 * Archive for Service directory
 *
 * @package phila-gov
 */
get_header(); ?>


<div class="row service directory">
  <div id="a-z-filter-list" class="columns results a-z-list medium-centered">
  <form class="search mbxl">
    <input class="search-field" type="text" placeholder="Begin typing to filter results by title, keyword, or acronym...">
    <input type="submit" class="search-submit" value="Search">
  </form>

<?php $args = array(
  'post_type'  => 'service_page',
  'posts_per_page'  => -1,
  'order' => 'ASC',
  'orderby' => 'title',
  'category__in' => phila_util_cat_ids(),
  'meta_query' => array(
    array(
      'key'     => 'phila_template_select',
      'value' => array('service_stub'),
      'compare' => 'NOT IN'
    ),
  ),
);

$service_pages = new WP_Query( $args ); ?>

<?php if ( $service_pages->have_posts() ) : ?>

  <?php
    $a_z = range('a','z');
    //fill keys with range, set all values to false
    $a_z = array_fill_keys($a_z, false);
    $service_title = array();
    $service_desc = array();
    $service_link = array();
    $service_parent = array();
  ?>

  <?php while ( $service_pages->have_posts() ) : $service_pages->the_post(); ?>

    <?php $terms = wp_get_post_terms( $post->ID, 'service_type' ); ?>

    <?php $page_terms['terms'] = array();?>

      <?php foreach ( $terms as $term ) : ?>
        <?php array_push($page_terms['terms'], $term->slug); ?>
      <?php endforeach; ?>
      <?php

      $desc['desc'] = phila_get_item_meta_desc( $blog_info = false );
      $link['link'] = get_permalink();

      if ( rwmb_meta('phila_service_alt_title', '', $post->ID) != '' ) {
        $title = rwmb_meta('phila_service_alt_title', '', $post->ID);
        $title_sanitized = sanitize_text_field($title);
        $service_title[$title] = $title;
        $service_desc[$title] = $desc;
        $service_link[$title] = $link;
      }else{
        $title = get_the_title();
        $title_sanitized = sanitize_text_field($title);
        $service_title[$title] = $title;
        $service_desc[$post->post_title] = $desc;
        $service_link[$post->post_title] = $link;
      }?>
      <?php
        //overwrite range array with values that exist
        $a_z[strtolower(substr($service_title[$title_sanitized], 0, 1 ))] = true; ?>
      <?php
      $services = array_merge_recursive($service_title, $service_desc, $service_link);

      asort($services);

      ?>
    <?php endwhile; ?>

  <div class="see-all-list">
  <?php foreach($a_z as $a_k => $a_v): ?>
    <?php if( $a_v == true ): ?>
      <div class="row collapse a-z-group" data-alphabet=<?php echo $a_k ?>>
        <div class="medium-4 columns">
          <div id="<?php echo $a_k ?>" class="letter separator"/><?php echo $a_k ?></div>
      </div>
        <div class="small-20 medium-20 columns">
        <?php endif; ?>
        <?php foreach( $services as $k => $v ) :?>
          <?php
            $first_c = strtolower($k[0]);

            if( $a_k == $first_c && isset( $v['link']) ) : ?>

              <div class="mvm" data-service="<?php echo isset($v['terms']) ? implode(', ', $v['terms'] ) : ''; ?>"  data-alphabet="<?php echo $a_k ?>">

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
      <div class="nothing-found h3"></div>
    </div>
  </div>
</div><!-- .row -->

<?php get_footer(); ?>
