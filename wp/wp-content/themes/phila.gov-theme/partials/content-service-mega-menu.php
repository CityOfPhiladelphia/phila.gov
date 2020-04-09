<?php
/**
 *
 * Partial for services mega menu display
 *
 *
**/

$service_menu_args  = array(
  'post_parent' => 0,
  'post_type' => 'service_page',
  'orderby' => 'title',
  'order'   => 'asc',
  'meta_query'  => array(
    array(
      'key' => 'phila_template_select',
      'value' => 'topic_page',
      'compare' => '='
    )
  )
);

?>
<?php $service_menu = new WP_Query( $service_menu_args ); ?>
<div id="services-list" class="global-nav show-for-medium">
  <div class="dropdown-pane mega-menu-dropdown" data-dropdown data-v-offset="0" data-hover-pane="true" data-trap-focus="true" data-hover="true" data-close-on-click="true" id="services-mega-menu">
    <div class="inner-wrapper">
      <div id="phila-menu-wrap" class="row expanded mbxs" data-equalizer data-equalize-by-row="true" data-services-menu>
        <?php if ( $service_menu->have_posts() ) : ?>
          <?php while ( $service_menu->have_posts() ) : $service_menu->the_post(); ?>
            <?php 
              $post_id = get_the_ID();
              $icon = phila_get_page_icon( $post_id );
            ?>
            <div class="medium-12 large-8 columns end">
              <div class="valign">
                <div class="valign-cell">
                  <a href="<?php echo get_the_permalink(); ?>">
                    <span>
                    <?php if ( !empty( $icon ) ) : ?>
                      <i class="fa-2x <?php echo $icon ?>" aria-hidden="true"></i> 
                    <?php endif; ?><div class="text-label"><?php echo the_title() ?></div>
                    </span>
                  </a>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        <?php endif;?>
        <?php #add dummy cell ?>
        <div class="hide-for-medium-only large-8 columns end bg-ghost-gray placeholder">
          <div class="valign"><div class="valign-cell"></div></div>
        </div>
        <div class="medium-12 large-8 float-right white left-arrow-indent bg-dark-ben-franklin mega-menu-footer">
          <div class="valign">
            <a href="/service-directory/" class="phl valign-cell service-directory"><span><i class="far fa-list fa-lg phm"></i>Service directory</span></a>
          </div>
        </div>  
      </div>
    </div>
  </div><!-- end service lvl 1 -->
</div><!-- end global nav-->
