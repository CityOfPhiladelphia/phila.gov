<?php
/**
 * Archive for Service Directory
 *
 * @package phila-gov
 */

get_header(); ?>

<div id="primary" class="content-area service directory">
  <main id="main" class="site-main">
    <div class="row">
      <header class="small-24 columns">
        <?php printf(__('<h1 class="contrast ptm">Service Directory</h1>', 'phila-gov') ); ?>
      </header>
    </div>
    <div class="row">
      <div class="medium-7 columns filter">
        <?php printf(__('<h2 class="h4 mtn">Filter by Service Categories</h2>', 'phila-gov') ); ?>
        <?php $terms = get_terms(
          array(
            'taxonomy' => 'service_type',
            'hide_empty' => true,
            )
        );?>
        <form id="service_filter">
          <ul class="no-bullet mam pan">
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
          array(
            'key'     => 'phila_template_select',
            'value'   => 'topic_page',
            'compare' => 'NOT IN',
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
            $service_title[$post->post_title] = $page_terms;
            $desc['desc'] = phila_get_item_meta_desc( $blog_info = false );
            $link['link'] = get_permalink();

            $service_desc[$post->post_title] = $desc;

            $service_link[$post->post_title] = $link;

            $services = array_merge_recursive($service_title, $service_desc, $service_link);

            ?>
          <?php endwhile; ?>
      <nav>
        <ul class="inline-list mbm pan mln h4">
          <?php foreach($a_z as $k => $v): ?>
            <?php //TODO: handle special characters and numbers in a better way ?>
            <?php $k_plain = preg_replace('/([^A-Za-z\-])/', '', $k);?>
            <?php if( $v == true && !empty( $k_plain) ) : ?>
              <li><a href="#<?php echo $k ?>" data-alphabet=<?php echo $k_plain ?>><?php echo strtoupper($k_plain); ?></a></li>
            <?php else : ?>
              <?php if ( !empty( $k_plain) ) : ?>
                <li><span class="ghost-gray"><?php echo strtoupper($k_plain);?></span></li>
              <?php endif; ?>
            <?php endif; ?>
          <?php endforeach; ?>
        </ul>
      </nav>
        <?php foreach($a_z as $a_k => $a_v): ?>
          <?php if( $a_v == true): ?>
            <div class="row collapse a-z-group" data-alphabet=<?php echo $a_k ?>>
                <div id="<?php echo $a_k ?>" class="medium-2 columns">
                  <span class="letter h1"><?php echo strtoupper($a_k); ?></span>
                </div>
                <div class="medium-21 columns">
            <?php endif; ?>
            <?php foreach($services as $k => $v) :?>
              <?php
                $first_c = strtolower($k[0]);
                if( $a_k == $first_c && $a_v == true ) : ?>
                  <div class="result mvm" data-service="<?php echo implode(', ', $v['terms'] ); ?>">
                    <a href="<?php echo $v['link']?>"><?php echo $k ?></a>
                    <p class="hide-for-small-only"><?php echo $v['desc'] ?></p>
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
  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>
