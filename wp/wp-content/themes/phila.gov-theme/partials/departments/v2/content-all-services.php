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
  <!-- <div class="medium-7 columns show-for-medium filter" data-desktop-filter-wrapper>
    <?php printf(__('<h2 class="h4 mtn">Filter by service category</h2>', 'phila-gov') ); ?>
    <?php $terms = get_terms(
      array(
        'taxonomy' => 'audience',
        'hide_empty' => true,
        )
    );?>
    <form id="service_filter">
      <ul class="no-bullet pan">
        <li><input type="checkbox" name="all" value="all" id="all" checked="checked"><label for="all">All Services</label></li>
        <?php foreach ( $terms as $term ) : ?>
          <li><input type="checkbox" name="<?php echo $term->slug ?>" value="<?php echo $term->slug ?>" id="<?php echo $term->slug ?>"><label for="<?php echo $term->slug ?>"><?php echo $term->name ?></label></li>
        <?php endforeach; ?>
      </ul>
    </form>
  </div> -->
<div id="a-z-filter-list" class="columns medium-16 results a-z-list medium-centered">

<?php $args = array(
  'post_type'  => 'service_page',
  'posts_per_page'  => -1,
  'order' => 'ASC',
  'orderby' => 'title',
  'category__in' => phila_util_cat_ids(),
  'meta_query' => array(
    'relation' => 'OR',
    array(
      'key'     => 'phila_template_select',
      'value' => array('default', 'tax_detail'),
      'compare' => 'IN'
    ),
    array(
      'relation' => 'AND',
        array(
          'key'     => 'phila_template_select',
          'value'   => 'topic_page',
          'compare' => 'IN',
        ),
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
  <!-- <form id="service-filter" class="search">
    <input class="search-field" type="text" placeholder="Begin typing to filter results by title or description" disabled="true">
    <input type="submit" class="search-submit" value="Search">
  </form> -->
  <div class="list">
  <?php foreach($a_z as $a_k => $a_v): ?>
    <?php if( $a_v == true ): ?>
      <div class="row collapse a-z-group" data-alphabet=<?php echo $a_k ?>>

        <hr id="<?php echo $a_k ?>" class="letter separator" data-alphabet="<?php echo $a_k ?>"/>

        <div class="small-20 medium-24 columns">
        <?php endif; ?>
        <?php foreach( $services as $k => $v ) :?>
          <?php
            $first_c = strtolower($k[0]);

            if( $a_k == $first_c && isset( $v['link']) ) : ?>

              <div class="small-21 columns result mvm" data-service="<?php echo isset($v['terms']) ? implode(', ', $v['terms'] ) : ''; ?>"  data-alphabet="<?php echo $a_k ?>">

                <a href="<?php echo isset( $v['link'] )?>"><?php echo $k ?><?php echo isset( $v['parent'] ) ? ' - ' . get_the_title ($v['parent']) : '' ?></a>
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
