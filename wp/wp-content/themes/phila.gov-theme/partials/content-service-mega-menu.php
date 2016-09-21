<?php
/**
 *
 * Partial for services mega menu display
 *
 *
**/

?>
<?php
  $service_args = array(
    'post_type' => 'service_page',
    'post_parent' => 0,
    'posts_per_page'  => -1,
    'order' => 'ASC',
    'orderby' => 'title'
  );
?>
<?php $top_service_pages = new WP_Query( $service_args ); ?>
<?php $i = 0; ?>
<?php if ( $top_service_pages->have_posts() ) : ?>
  <div class="global-nav">
    <div class="dropdown-pane" id="services-mega-menu" data-dropdown data-options="closeOnClick:true; hover: true; hoverPane: true">

      <?php while ( $top_service_pages->have_posts() ) : $top_service_pages->the_post(); ?>
        <?php $i++;?>

        <?php if ( $i % 3 == 1 ) :?>
          <div class="row expanded mbxs">
        <?php endif; ?>
          <div class="medium-8 columns end">
            <a href="<?php echo get_the_permalink(); ?>"><?php echo get_the_title(); ?></a>
          </div>
        <?php if ( $i % 3 == 0 ) :?>
          </div>
        <?php endif;?>
    <?php endwhile; ?>
    </div>
    <div class="row expanded collapse bg-ghost-gray mega-menu-footer">
      <div class="medium-8 float-right white bg-ben-franklin-blue left-arrow-indent">
        <div class="valign">
          <a href="" class="phl valign-cell service-directory">Services Directory</a>
        </div>
      </div>
    </div>
  </div><!-- end service lvl 1 -->
</div><!-- end global nav-->
<?php wp_reset_postdata();?>

<?php endif; ?>
