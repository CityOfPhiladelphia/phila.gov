<?php 
  $accordion_group = rwmb_meta( 'accordion_group' );
  $requirements_prereq_title = rwmb_meta('accordion_row_title');
?>
<?php if ( !empty( $accordion_group ) ): 
  ?>
  <section>
    <div class="mbxxl">
      <h3 id="<?php echo sanitize_title_with_dashes($requirements_prereq_title) ?>" class="phm-mu mtl mbm"><?php echo $requirements_prereq_title ?></h3>
      <?php
        $accordion_title = '';
        $is_full_width = false; 
        $use_icon = true;
        $custom_icon = true;?>
        <?php include(locate_template('partials/global/accordion.php')); ?>
    </div>
  </section>
<?php endif ?>