<?php
/*
 * Possible values:
  $see_all['content_type']
  $see_all['is_full']
  $see_all['URL']
  $see_all['nice_name']
  $override_url 
 *
*/?>
<a class="button float-right <?php echo isset($see_all['content_type']) ? 'content-type-' . $see_all['content_type']  : ''?>
  <?php echo (isset( $see_all['is_full'] ) && $see_all['is_full']) ? 'full' : ''?>" href="<?php echo !empty( $override_url ) ? $override_url : $see_all['URL'] ?>" aria-label="See all <?php echo $see_all['nice_name'] ?> "> View all <?php echo $see_all['nice_name'] ?> <i class="fas fa-angle-right fa-lg" aria-hidden="true"></i>
</a>
