<?php
/**
 * The template used for displaying Event Spotlight Pages
 *
 * @package phila-gov
 */
?>
<?php

  // MetaBox variables
  $page_rows = rwmb_meta('spotlight_row');
  //var_dump($page_rows);
?>
<div>
<?php
  foreach ($page_rows as $key => $value):
    $current_row = $page_rows[$key];
    //var_dump($current_row);
    ?>
    <!-- Grid Row -->
      <?php if ( $current_row['spotlight_options'] == 'free_text'): ?>
        <?php if ( isset( $current_row['free_text_option'] ) ):
          $custom_text = $current_row['free_text_option']; ?>
          <!-- Custom Text -->
          <section class="row mvl">
            <div class="large-24 column">
            <h2><?php echo $custom_text['phila_custom_wysiwyg']['phila_wysiwyg_title'] ?></h2>
            <div>
              <?php echo $custom_text['phila_custom_wysiwyg']['phila_wysiwyg_content'] ?>
            </div>
            </div>
          </section>
        <?php endif; ?>
      <?php elseif ( $current_row['spotlight_options'] == 'registration'): ?>
        <?php if ( isset( $current_row['phila_registration'] ) ):

          $registration = $current_row['phila_registration']; ?>

          <?php include(locate_template('partials/programs/registration.php')); ?>
        <?php endif; ?>

      <?php elseif ( $current_row['spotlight_options'] == 'call_to_action_multi'): ?>
        <!-- Display Multi Call to Action as Resource List -->
        <?php if ( !isset( $current_row['call_to_action_multi']) ):

          $phila_dept_homepage_cta =
           $current_row['call_to_action_multi_row']['phila_call_to_action_section'];
            include(locate_template('partials/departments/phila_call_to_action_multi.php')); ?>

        <?php endif; ?>
      <?php elseif ( $current_row['spotlight_options'] == 'calendar'): ?>
        <?php if(!isset($current_row['calendar_row'])): ?>
          <?php include(locate_template('partials/departments/phila_module_row_2.php')); ?>
        <?php endif;?>

    <?php endif;  /*end full row */?>

        </section>
    <!-- Grid Row -->
    <?php endforeach; ?>
</div>
