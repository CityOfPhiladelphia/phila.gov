<?php
/*
 * End of post call to action links
 *
*/
?>
<?php $cta_meta = rwmb_meta('post_read_cta');?>
<?php $cta = phila_loop_clonable_metabox($cta_meta);?>

<?php foreach( $cta as $a ) : ?>
  <?php if ( isset($a['phila_post_links']['link_text'] ) ) : ?>
    <div class="mbm">
      <div class="h4"><a href="<?php echo $a['phila_post_links']['link_url']?>" class="<?php echo isset($a['phila_post_links']['is_external'] ) ? 'external ': '';?>"><i class="fa fa-arrow-right"></i> <?php echo $a['phila_post_links']['link_text']?></a></div>
      <div class="description mlm">
        <?php echo isset( $a['phila_link_desc'] ) ? $a['phila_link_desc'] : ''; ?>
      </div>
    </div>
  <?php endif ?>
<?php endforeach; ?>
