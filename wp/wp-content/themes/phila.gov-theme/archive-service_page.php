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
      <div id="a-z-filter-list" class="medium-16 columns results a-z-list">
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

            foreach ( $contextual_pages as $id ) {
              if($id === $post->ID) {
                $parent['parent'] = $post->post_parent;
                $service_parent[$post->post_title] = $parent;
                $service_title[$post->post_title] = $page_terms;
                $service_desc[$post->post_title] = $desc;
                $service_link[$post->post_title] = $link;
              }
            }

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
      <form id="service-filter" class="search">
        <input class="search-field fuzzy-search" type="text" placeholder="Begin typing to filter results by title or description">
        <input type="submit" class="search-submit" value="Search">
      </form>
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
          <?php if( $a_v == true ): ?>
            <div class="row collapse a-z-group" data-alphabet=<?php echo $a_k ?>>
              <hr id="<?php echo $a_k ?>" class="letter separator" data-alphabet="<?php echo $a_k ?>"/>

              <div class="small-20 medium-24 columns">
              <?php endif; ?>
              <?php foreach( $services as $k => $v ) :?>
                <?php
                  $first_c = strtolower($k[0]);
                  if( $a_k == $first_c && $a_v == true ) : ?>
                    <div class="small-21 columns result mvm" data-service="<?php echo isset($v['terms']) ? implode(', ', $v['terms'] ) : ''; ?>"  data-alphabet="<?php echo $a_k ?>">
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
  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>
