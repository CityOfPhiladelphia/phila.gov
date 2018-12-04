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

<div class="row">
  <div id="a-z-filter-list" class="columns results a-z-list border-top-none">
    <?php
      $get_pages_args = array(
        'hierarchical' => 0,
        'meta_key' => 'phila_hide_children',
        'meta_value' => '1',
        'post_type' => 'service_page',
      );
      $pages = get_pages($get_pages_args);

      $children_array = array();
      $hidden_pages  = array();

      foreach ( $pages as $page ){
        $args = array(
          'post_parent' => $page->ID,
        );
        $children_array[] = get_children( $args );
      }

      foreach ($children_array as $key => $value) {
        foreach ($value as $child_key => $child_value) {
          array_push($hidden_pages, $child_key);
        }
      } ?>

    <?php $args = array(
      'post_type'  => 'service_page',
      'posts_per_page'  => -1,
      'order' => 'ASC',
      'orderby' => 'title',
      'category__in' => phila_util_cat_ids(),
      'post__not_in' => $hidden_pages,
      'meta_query' => array(
        'relation' => 'OR',
        array(
          'key'     => 'phila_template_select',
          'value' => array( 'service_stub' ),
          'compare' => 'NOT IN'
        ),
      ),
    );

    $service_pages = new WP_Query( $args ); ?>

    <?php if ( $service_pages->have_posts() ) : ?>

      <?php
        $a_z = range('a','z');
        $a_z = array_fill_keys($a_z, false);
        $service_title = array();
        $service_desc = array();
        $service_link = array();
        $service_parent = array();
      ?>

      <?php while ( $service_pages->have_posts() ) : $service_pages->the_post(); ?>

      <?php
        //overwrite range array with values that exist
        $a_z[strtolower(substr($post->post_title, 0, 1 ))] = true; ?>

        <?php $terms = wp_get_post_terms( $post->ID, 'service_type' ); ?>

        <?php $page_terms['terms'] = array();?>

          <?php foreach ( $terms as $term ) : ?>
            <?php array_push($page_terms['terms'], $term->slug); ?>
          <?php endforeach; ?>
          <?php

          $desc['desc'] = phila_get_item_meta_desc( $blog_info = false );
          $link['link'] = get_permalink();

          if ( rwmb_meta('phila_service_alt_title', '', $post->ID) != null ) {
            $alt_title = rwmb_meta('phila_service_alt_title', '', $post->ID);
            $service_title[$alt_title] = $alt_title;
            $service_title[$alt_title] = $page_terms;
            $service_desc[$alt_title] = $desc;
            $service_link[$alt_title] = $link;
          }else{
            $service_title[$post->post_title] = $page_terms;
            $service_desc[$post->post_title] = $desc;
            $service_link[$post->post_title] = $link;
          }?>
          <?php
          $services = array_merge_recursive($service_title, $service_desc, $service_link, $service_parent);
          ?>
        <?php endwhile; ?>
    <nav class="show-for-medium">
      <ul class="inline-list mbm pan mln h4">
        <?php foreach($a_z as $k => $v): ?>
          <?php //TODO: handle special characters and numbers in a better way ?>
          <?php $k_plain = preg_replace('/([^A-Za-z\-])/', '', $k);?>
          <?php if( $v == true && !empty( $k_plain) ) : ?>
            <li><a href="#<?php echo $k ?>" data-alphabet=<?php echo $k_plain ?> class="scrollTo"><?php echo strtoupper($k_plain); ?></a></li>
          <?php else : ?>
            <?php if ( !empty( $k_plain ) ) : ?>
              <li><span class="ghost-gray"><?php echo strtoupper( $k_plain );?></span></li>
            <?php endif; ?>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>
    </nav>
    <div class="list">
    <?php foreach($a_z as $a_k => $a_v): ?>
      <?php if( $a_v == true): ?>
        <div class="row collapse a-z-group" data-alphabet=<?php echo $a_k ?>>
          <hr id="<?php echo $a_k ?>" class="letter separator" data-alphabet="<?php echo $a_k ?>"/>

<!--
            <div class="small-3 medium-2 columns">
              <span class="letter h1" id="<?php echo $a_k ?>"><?php echo strtoupper($a_k); ?></span>
            </div> -->
            <div class="small-20 medium-21 columns">
        <?php endif; ?>
        <?php foreach($services as $k => $v) :?>
          <?php
            $first_c = strtolower($k[0]);
            if( $a_k == $first_c && $a_v == true ) : ?>
            <div class="columns result mvm" data-service="<?php echo isset($v['terms']) ? implode(', ', $v['terms'] ) : ''; ?>"  data-alphabet="<?php echo $a_k ?>">
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
  </div>
</div> <!-- .row -->

<?php get_footer(); ?>
