<?php
/*
 * Possible values:
 $see_all['content_type']
 $see_all['is_full']
 $see_all['URL']
 $see_all['nice_name']
 *
*/?>

<a class="button float-right <?php echo isset($see_all['content_type']) ? 'content-type-' . $see_all['content_type']  : ''?>
  <?php echo isset( $see_all['is_full'] ) ? 'full' : ''?>" href="<?php echo $see_all['URL'] ?>" aria-label="See all <?php echo $see_all['nice_name'] ?> "> See all <?php echo $see_all['nice_name'] ?> <i class="fa fa-angle-right fa-lg" aria-hidden="true"></i>
</a>
