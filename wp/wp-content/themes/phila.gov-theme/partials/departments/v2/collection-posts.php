<!-- Collection posts -->
<?php $collection_post_count = 0; ?>
<div class="row one-quarter-row mvl collection-posts">
  <div class="columns medium-6">
      <?php if( isset($current_row['posts']['phila_wysiwyg_title'] )): ?>	
        <?php $current_row_id = sanitize_title_with_dashes( $current_row['posts']['phila_wysiwyg_title']);?>
        <h3 id="<?php echo $current_row_id;?>"><?php echo $current_row['posts']['phila_wysiwyg_title']; ?></h3>
      <?php endif;?>	
  </div>
  <div class="columns medium-18 pbxl">
    <div class="mbl">	
    <?php foreach( $current_row['posts']['phila_post_picker'] as $collection_post_id ) : ?>
      <?php $collection_post_count++; ?>
      <?php if ($collection_post_count < 4) {  ?>
        <article id="post-<?php the_ID($collection_post_id); ?>" class="mbm">
          <a href="<?php echo the_permalink($collection_post_id); ?>" class="card flex-container flex-dir-row full-height">
            <div class="grid-x flex-dir-column">
              <div class="flex-child-shrink">
                <?php if ( has_post_thumbnail($collection_post_id) ) : ?>
                    <?php echo phila_get_thumbnails($collection_post_id); ?>
                <?php endif; ?>
              </div>
              <div class="card--content pam flex-child-auto">
                <div class="cell align-self-top post-label">
                  <header class="cell mvm">
                    <h3><?php echo get_the_title($collection_post_id); ?></h3>
                  </header>
                </div>
                <div class="cell align-self-bottom">
                  <div class="post-meta">
                    <span class="date-published"><time datetime="<?php echo get_post_time('Y-m-d'); ?>"><?php echo get_the_date('', $collection_post_id);?></time></span>
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
</div>      
<!-- / Collection posts -->