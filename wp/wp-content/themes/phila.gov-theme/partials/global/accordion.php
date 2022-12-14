<?php
  /* Required vars:
    $is_full_width - Boolean - Use old style with gray bg and full-width click, or new style with expand/collapse
    $accordion_title - string 
    $accordion_group - array
    Optional 
    $accordion_icon - optional
    $use_icon = Boolean - determine if icon should be displayed when using non-full width template
  */

?>

<?php if ( $is_full_width === true) : ?>  
<section class="mvl">
  <h2 id="#<?php echo phila_format_uri($accordion_title)?>"><?php echo isset($accordion_title) ? $accordion_title : '' ?></h2>
  <div class="accordion" data-accordion data-multi-expand="true">
    <?php foreach ($accordion_group as $accordion ) : ?>
      <div class="accordion-item" data-accordion-item>
        <a href="#" class="accordion-title no-p-margin"><?php echo $accordion['phila_custom_wysiwyg']['phila_wysiwyg_title'] ?></a>
        <div class="accordion-content" data-tab-content>
        <?php echo apply_filters('the_content', $accordion['phila_custom_wysiwyg']['phila_wysiwyg_content']); ?>
        </div>
      </div>
    <?php endforeach;?>
  </div>
</section>
<?php else: ?>
  <?php foreach ( $accordion_group as $ag_key => $accordion ) : ?>
  <?php reset($accordion_group) ?>
  <?php if (isset( $accordion['phila_custom_wysiwyg']['phila_wysiwyg_title'] ) ): ?>
  <div class="icon-expand-container result grid-x align-bottom">
    <div class="icon-expand-title align-middle cell auto">
      <div class="cell auto no-p-margin fixed-width small-21 grid-x">
      <?php if ($use_icon === true ) :?>
        <?php 
        if (!empty($override_icon)){
          $accordion_icon = $override_icon;
        }else if ( !empty( $accordion['phila_custom_wysiwyg']['phila_accordion_icon'] ) ) {
          $accordion_icon =  $accordion['phila_custom_wysiwyg']['phila_accordion_icon'];
        }else {
          $accordion_icon = 'fas fa-tasks';
        }
      ?>
      <div class="cell shrink hide-for-small-only prs"><i class="<?php echo $accordion_icon ?> fa-2x fa-fw"></i></div> 
      <?php endif; ?>
        <div class="cell auto"><?php echo apply_filters( 'the_content', $accordion['phila_custom_wysiwyg']['phila_wysiwyg_title'] ); ?></div>
      </div>
    </div>
    <a href="#" data-toggle="icon-expand" class="cell shrink" id="<?php echo sanitize_title_with_dashes($accordion['phila_custom_wysiwyg']['phila_wysiwyg_title']) . '-control' ?>"> More + </a>
    <div class="icon-expand-content mvm cell small-24" aria-controls="<?php echo sanitize_title_with_dashes($accordion['phila_custom_wysiwyg']['phila_wysiwyg_title']) . '-control' ?>" aria-expanded="false">
      <?php echo apply_filters( 'the_content', $accordion['phila_custom_wysiwyg']['phila_wysiwyg_content']); ?>
    </div>
  </div>
  <?php endif; ?>

  <?php end( $accordion_group) ; ?>
  <?php if ($ag_key != key($accordion_group) ) :?>
    <hr class="icon-expand-hr">
  <?php endif ?>
  <?php endforeach;?>
<?php endif; ?>