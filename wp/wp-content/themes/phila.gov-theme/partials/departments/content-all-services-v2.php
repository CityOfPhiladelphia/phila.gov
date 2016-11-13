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

<div id="primary" class="content-area service directory">
  <main id="main" class="site-main">
    <div class="row">
      <div class="columns results a-z-list">
      <?php
      $categories = get_the_category();
      $cat_ids = array();
      foreach ($categories as $category ){
        array_push($cat_ids, $category->cat_ID);
      }
      $args = array(
        'post_type'  => 'service_page',
        'posts_per_page'  => -1,
        'order' => 'ASC',
        'orderby' => 'title',
        'category__in' => $cat_ids,
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
              array(
                'key' => 'phila_is_contextual',
                'value' => '1',
                'compare' => '=' ,
              ),
            ),
          ),
      );
      $service_pages = new WP_Query( $args ); ?>

      <?php
      //TODO: clean up this rushed hackjob
        $get_pages_args = array(
          'hierarchical' => 0,
          'meta_key' => 'phila_is_contextual',
          'meta_value' => '1',
          'post_type' => 'service_page',
        );
        $pages = get_pages($get_pages_args);

        $children_array = array();
        $contextual_pages  = array();

        foreach ( $pages as $page ){
          $args = array(
            'post_parent' => $page->ID,
          );
          $children_array[] = get_children( $args, ARRAY_A );
        }
        foreach ($children_array as $k => $v){
          foreach ($v as $arr => $thing){
            array_push($contextual_pages, $thing['ID']);
          }
        }
        ?>

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

          <?php $terms = wp_get_post_terms( $post->ID, 'service_type' ); ?>

          <?php $page_terms['terms'] = array();?>

            <?php foreach ( $terms as $term ) : ?>
              <?php array_push($page_terms['terms'], $term->slug); ?>
            <?php endforeach; ?>

            <?php
              //overwrite range array with values that exist
              $a_z[strtolower(substr($post->post_title, 0, 1 ))] = true; ?>

            <?php

            foreach ( $contextual_pages as $id ) {
              if($id === $post->ID) {
                $parent['parent'] = $post->post_parent;
                $service_parent[$post->post_title] = $parent;
              }
            }

            $service_title[$post->post_title] = $page_terms;
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
                  <div class="result mvm" data-service="<?php echo implode(', ', $v['terms'] ); ?>">
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
    <div id="mobile-filter" class="reveal filter full" data-reveal data-options="closeOnClick:false;">
      <div class="inner-modal">
        <div class="row">
          <div class="small-24 columns pts">
            <button class="close-button float-right ben-franklin-blue" data-close aria-label="Close modal" type="button">
              <i class="fa fa-times" aria-hidden="true"></i>
            </button>
          </div>
          <div class="columns">
            <h2>Filter by service category</h2>
          </div>
        </div>
        <div class="row">
          <div class="small-12 columns">
            <a href="#" class="button full-width pan clearfix" aria-label="Clear filter selections" data-clear-filter>
              <div class="valign">
                <div class="button-label center valign-cell h2">Clear</div>
              </div>
            </a>
          </div>
          <div class="small-12 columns">
            <a href="#" class="button full-width pan clearfix" data-close aria-label="Apply modal" data-apply-filter>
              <div class="valign">
                <div class="button-label center valign-cell h2">Apply</div>
              </div>
            </a>
          </div>
        </div>
        <div class="row">
          <div class="columns mvm">
            <span>Choose all that apply.</span>
          </div>
          <div class="small-24 columns">
            <div data-toggle="data-mobile-filter">
            </div>
          </div>
        </div>
      </div>
    </div><!-- #mobile-filter -->
  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>
