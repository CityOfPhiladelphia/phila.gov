<?php if ( !empty( $accordion_group ) ):

  $user_selected_template = rwmb_meta( 'phila_template_select', $args = array(), $post->id ); ?>

<!-- Program page accordion -->
  <section>
    <div class="grid-container mbxxl">
      <h3 id="<?php echo sanitize_title_with_dashes($requirements_prereq_title) ?>" class="<?php echo $user_selected_template === 'custom_content' ? 'black bg-ghost-gray phm-mu mbm' : 'h4' ?>mtl mbm"><?php echo $requirements_prereq_title ?></h3>
      <?php
        $accordion_title = '';
        $is_full_width = true;
        $use_icon = true;
        $custom_icon = true;?>
        <?php include(locate_template('partials/global/accordion.php')); ?>
    </div>
  </section>
  <!-- /Program page accordion -->

<?php endif; ?>