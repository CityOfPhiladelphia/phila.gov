<?php
/*
 *
 * Display department logo, if it exists
 *
 */

?>
<?php
  $id = '';
  $page_id = get_queried_object_id();
  $parent = get_post_ancestors( $page_id );
  if ( empty($parent) ) {
    $id = $page_id;
  }else{
    $id = end($parent);
  }
?>
<?php if( null !== phila_get_department_logo_v2($id) && !is_archive()  ) : ?>
  <div class="row mtxl">
    <div class="columns center">
      <img src="<?php echo phila_get_department_logo_v2( $id ) ?>" class="department-logo" alt="">
    </div>
  </div>
<?php endif;?>


<?php if (get_post_type() == 'guides') :
  $ids = phila_get_department_owner_ids($categories = get_the_category());
  if ($ids !== null) : ?>
    <div class="grid-container footer-logos">
      <div class="grid-x">
        <?php foreach ($ids as $id => $slug) :?>
          <div class="cell center medium-<?php echo phila_grid_column_counter(sizeof($ids))?>">
            <img src="<?php echo phila_get_department_logo_v2($id); ?>" src="">
          </div>
        <?php endforeach; ?>
        </div>
      </div>
  <?php endif; ?>
<?php endif; ?>