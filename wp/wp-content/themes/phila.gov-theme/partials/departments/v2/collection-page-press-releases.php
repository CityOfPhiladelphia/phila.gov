<!-- Collection page press releases -->
<?php $collection_post_count = 0; ?>
<div class="row one-quarter-row mvl collection-press_releases">
  <div class="columns medium-6">
      <?php if( isset($current_row['press_releases']['phila_wysiwyg_title'] )): ?>
        <?php $current_row_id = sanitize_title_with_dashes( $current_row['press_releases']['phila_wysiwyg_title']);?>
        <h3 id="<?php echo $current_row_id;?>"><?php echo $current_row['press_releases']['phila_wysiwyg_title']; ?></h3>
      <?php endif;?>
  </div>
  <div class="columns medium-18 pbxl">
    <div class="mbl">
    <?php foreach( $current_row['press_releases']['phila_post_picker'] as $collection_post_id ) : ?>
      <?php
        global $post;
        $post = get_post( $collection_post_id, OBJECT );
        setup_postdata( $post );
      ?>
      <?php $collection_post_count++; ?>
      <?php if ($collection_post_count < 4) {  ?>
        <article id="post-<?php the_ID(); ?>" class="mbm flex-container row">
          <?php if ( has_post_thumbnail() ) : ?>
          <div class="columns medium-8">
            <?php echo phila_get_thumbnails(); ?>
          </div>
          <?php endif; ?>
          <a href="<?php echo the_permalink(); ?>" class="card flex-dir-row full-height columns medium-16">
            <div class="grid-x flex-dir-column">
              <div class=" card--content prm pvm flex-child-auto">
                <div class="cell align-self-top post-label">
                  <header class="cell mvs">
                    <h3><?php echo get_the_title(); ?></h3>
                  </header>
                </div>
                <div class="cell align-self-bottom">
                  <div class="post-meta">
                    <span class="date-published"><time datetime="<?php echo get_post_time('Y-m-d'); ?>"><?php echo get_the_date();?></time></span>
                  </div>
                </div>
              </div>
            </div>
          </a>
        </article>
      <?php } ?>
    <?php endforeach; ?>
    </div>
  </div>
  <?php if ( $current_row['press_releases'] && isset($current_row['press_releases']['phila_v2_press_release_link']) ) :?>
    <div class="float-right">
      <a href="<?php echo $current_row['press_releases']['phila_v2_press_release_link']?>">See all press releases ></a>
    </div>
    <?php endif; ?>
</div>
<?php wp_reset_postdata(); ?>
<!-- / Collection press_releases -->