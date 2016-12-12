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
