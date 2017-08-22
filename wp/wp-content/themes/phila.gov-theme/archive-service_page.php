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
      <header class="small-24 columns">
        <?php printf(__('<h1 class="contrast ptm">Service directory</h1>', 'phila-gov') ); ?>
      </header>
    </div>
    <div class="row mbl show-for-small-only">
      <div class="small-24 columns mbm">
        <a data-open="mobile-filter" class="button full-width pan clearfix">
          <div class="center h2">Filter by service category</div>
        </a>
      </div>
      <div class="small-12 columns">
        <a href="#" class="button full-width pan clearfix" data-alpha-order>
          <div class="valign">
            <div class="button-label center valign-cell h2">A-Z</div>
          </div>
        </a>
      </div>
      <div class="small-12 columns">
        <a href="#" class="button outline full-width pan clearfix" data-reverse-alpha-order>
          <div class="valign">
            <div class="button-label center valign-cell h2">Z-A</div>
          </div>
        </a>
      </div>
    </div>
    <div class="row">
      <div class="medium-7 columns show-for-medium filter" data-desktop-filter-wrapper>
        <?php printf(__('<h2 class="h4 mtn">Filter by service category</h2>', 'phila-gov') ); ?>
        <?php $terms = get_terms(
          array(
            'taxonomy' => 'service_type',
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
      </div>
      <div class="medium-16 columns results a-z-list">
      <?php $args = array(
        'post_type'  => 'service_page',
        'posts_per_page'  => -1,
        'order' => 'ASC',
        'orderby' => 'title',
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
            <button class="close-button float-right dark-ben-franklin bg-white" data-close aria-label="Close modal" type="button">
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
