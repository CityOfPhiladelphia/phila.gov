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
<?php  $logo = phila_get_department_logo_v2( $id );?>
  <div class="row mtxl">
    <div class="columns center">
      <img src="<?php echo $logo['full_url'] ?>" class="department-logo" alt="<?php echo $logo['alt'] ?>">
    </div>
  </div>
<?php endif;?>


<?php if (get_post_type() == 'guides') :
  $ids = phila_get_department_owner_ids($categories = get_the_category());
  if ($ids !== null) : ?>

    <div class="grid-container footer-logos">
      <hr>
      <div class="grid-x align-center">
        <?php foreach ($ids as $id => $slug) :?>
          <?php $img = rwmb_meta( 'phila_v2_department_logo', $args = array( 'size' => 'full', 'limit' => 1 ), $id ); ?>
          <div class="cell center medium-6">
            <img src="<?php echo $img[0]['full_url'] ?>" class="department-logo" alt="<?php echo $img[0]['alt'] ?>">
          </div>
        <?php endforeach; ?>
        </div>
      </div>
  <?php endif; ?>
<?php endif; ?>