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
      <div class="row bdr-bottom bdr-sidewalk mtm">
        <div class="grid-x">
          <div class="side-menu medium-7 columns bdr-right bdr-sidewalk hide-for-small-only pbxl">
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
        <div class="cell medium-16 columns pbxl flex-container auto mrn margin-auto">
          <article class="full">
            <header class="entry-header">
              <h2><?php echo ( $parent_title != get_the_title() ) ?  get_the_title() : '' ?></h2>
            </header>
            <div id="service-<?php echo $user_selected_template?>" data-swiftype-index='true' data-swiftype-name="body" data-swiftype-type="text" class="entry-content">
            <?php if ($user_selected_template == 'tax_detail') : 
              get_template_part('partials/services/content', 'tax-detail');
            
              elseif ($user_selected_template == 'start_process') : 
                get_template_part('partials/services/content', 'start-process');
                get_template_part('partials/content', 'default'); 

              elseif ($user_selected_template == 'vue_app') : 
                get_template_part('partials/services/content', 'vue-app');

              elseif ($user_selected_template == 'default_v2') :
                get_template_part('partials/services/content', 'default-v2'); 
              
              elseif ($user_selected_template == 'custom_content') :
                include( locate_template( 'partials/content-basic.php' ) ); 
                include( locate_template( 'partials/content-phila-row.php' ) ); 
            
            ?> <!-- Service Stub  --> <?php
              elseif ($user_selected_template == 'service_stub') : 
                get_template_part('partials/services/content', 'stub'); 
              elseif ($user_selected_template == 'topic_page'):
                get_template_part('partials/services/content', 'topic-page'); 

              else : 
                get_template_part('partials/content', 'default'); 

              endif; ?>
              </div>
            </article>
          </div>
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
