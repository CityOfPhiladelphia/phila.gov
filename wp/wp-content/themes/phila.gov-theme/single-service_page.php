<?php
/**
 * This is the template that displays all service pages by default.
 *
 * @package phila-gov
 */
 get_header(); ?>

<div id="primary" class="content-area">
  <main id="main" class="site-main">

    <?php while ( have_posts() ) : the_post(); ?>
    <?php $user_selected_template = phila_get_selected_template(); ?>
    <?php
      $parent_anc = get_post_ancestors ( $post );
      $parent_id = array_pop( $parent_anc );
      $parent_title = get_the_title( $parent_id );
      if ( empty( $parent_id ) ) {
        $parent_id = $post->ID;
      }
      $icon = phila_get_page_icon( $parent_id );
      ?>
    <div id="post-<?php the_ID(); ?>">
      <div class="row">
        <header class="small-24 columns">
          <h1 class="contrast">
            <?php if ( !empty( $icon ) ) : ?>
              <i class="<?php echo $icon ?>" aria-hidden="true"></i>
            <?php endif; ?>
            <?php echo $parent_title; ?>
          </h1>
        </header>
        </div>
        <div class="row equal-height bdr-bottom bdr-sidewalk mtm">
          <div class="side-menu medium-7 columns bdr-right bdr-sidewalk equal hide-for-small-only pbxl">
            <nav data-swiftype-index="false" id="side-nav">
              <ul id="menu-<?php echo sanitize_title( $parent_title )?>" class="services-menu vertical menu">
              <?php
                $args = array(
                  'post_type' => 'service_page',
                  'sort_column' => 'menu_order, title',
                  'order' => 'ASC',
                  'title_li' => '',
                  'child_of'  => $parent_id,
                  'link_before' => '<span>',
                  'link_after'  => '</span>',
                );
                wp_list_pages($args);
              ?>
              </ul>
            </nav>
        </div>
        <div class="medium-16 columns equal pbxl">
          <article>
            <header class="entry-header">
              <h2><?php echo ( $parent_title != get_the_title() ) ?  get_the_title() : '' ?></h2>
            </header>
            <div data-swiftype-index='true' data-swiftype-name="body" data-swiftype-type="text" class="entry-content">
            <?php if ($user_selected_template == 'tax_detail') : ?>
              <?php get_template_part('partials/services/content', 'tax-detail');?>
            
            <?php elseif ($user_selected_template == 'start_process') : ?>
              <?php get_template_part('partials/services/content', 'start-process');?>
              <?php get_template_part('partials/content', 'default'); ?>

            <?php elseif ($user_selected_template == 'vue_app') : ?>
              <?php get_template_part('partials/services/content', 'vue-app');?>
              <?php //get_template_part('partials/content', 'default'); ?>

            <?php elseif ($user_selected_template == 'default_v2') :?>
              <?php get_template_part('partials/services/content', 'default-v2'); ?>
            
            <!-- Service Stub  -->
            <?php elseif ($user_selected_template == 'service_stub') : ?>
              <?php if ( null !== rwmb_meta( 'phila_stub_source' ) ) : ?>
                <?php $stub_source = rwmb_meta( 'phila_stub_source' );?>
                <?php $post_id = intval( $stub_source );?>

                  <?php $stub_args = array(
                    'p' => $post_id,
                    'post_type' => 'service_page'
                    ); ?>

                  <?php $stub_post = new WP_Query($stub_args); ?>
                  <?php if ( $stub_post->have_posts() ): ?>
                    <?php while ( $stub_post->have_posts() ) : ?>
                      <?php $stub_post->the_post(); ?>
                      <?php $source_template =  rwmb_meta( 'phila_template_select'); ?>
                        <?php if ($source_template == 'default_v2') :?>
                          <?php get_template_part('partials/services/content', 'default-v2'); ?>

                        <?php elseif ($source_template == 'default') : ?>
                          <?php get_template_part('partials/content', 'default'); ?>

                        <?php elseif ($source_template == 'tax_detail') : ?>
                          <?php get_template_part('partials/services/content', 'tax-detail'); ?>

                        <?php elseif ($source_template == 'topic_page') : ?>
                          <?php get_template_part('partials/services/content', 'topic-page'); ?>

                        <?php elseif ($source_template == 'start_process') : ?>
                          <?php get_template_part('partials/services/content', 'start-process'); ?>

                        <?php endif; ?>
                      <?php endwhile; ?>
                    <?php endif; ?>
                    <?php wp_reset_query(); ?>
                  <?php endif; ?>
                  <!-- END Service Stub -->
                <?php elseif ($user_selected_template == 'topic_page'):?>
                  <?php get_template_part('partials/services/content', 'topic-page'); ?>

                <?php else : ?>
                  <?php get_template_part('partials/content', 'default'); ?>

              <?php endif; ?>
              </div>
            </article>
          </div>
        </div>
    </div><!-- #post-## -->


  <?php  endwhile; // end of the loop. ?>

  </main><!-- #main -->
</div><!-- #primary -->

<?php get_footer(); ?>
<script>
  function serviceMenuClick(obj){
    window.dataLayer.push({
      'event' : 'GAEvent', 
      'eventCategory': 'Service Page Conversion', 
      'eventAction': '<?php echo $parent_title ?>', 
      'eventLabel': obj.innerText, 
    })
  }
</script>
