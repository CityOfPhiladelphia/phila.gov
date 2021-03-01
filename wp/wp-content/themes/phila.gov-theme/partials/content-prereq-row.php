<?php 
  $accordion_group = rwmb_meta( 'accordion_group' );
  $requirements_prereq_title = rwmb_meta('accordion_row_title');
  $override_icon = rwmb_meta('phila_v2_icon');
  $user_selected_template = rwmb_meta( 'phila_template_select', $args = array(), $post->id ); 
  ?>
<?php if ( !empty( $accordion_group ) ): ?>
  <section>
    <div class="mbxxl">
      <h3 id="<?php echo sanitize_title_with_dashes($requirements_prereq_title) ?>" class="<?php echo $user_selected_template === 'custom_content' ? 'black bg-ghost-gray phm-mu mbm' : 'h4' ?> mtl mbm"><?php echo $requirements_prereq_title ?></h3>
      <?php
        $accordion_title = '';
        $is_full_width = false; 
        $use_icon = true;
        $custom_icon = true;?>
        <?php include(locate_template('partials/global/accordion.php')); ?>
    </div>
  </section>
<?php endif ?>