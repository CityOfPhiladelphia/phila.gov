<?php
  /* Partial for project staff accordion-style rendering. */
  $members = rwmb_meta('phila_project_staff_list'); 
  if ( !isset( $section_title ) ) :
    $section_title = rwmb_meta('section_title');
  endif;
  if ( !isset( $members ) ) :
    $members = rwmb_meta('phila_members_list');  
  endif;
  if ( !isset( $table_cell_title ) ) :
    $table_cell_title = rwmb_meta('table_head_title');
  endif;
?>

<?php if ( !empty( $members ) ) : ?>
<!-- Project Staff members -->
<div class="grid-container mvxl">
  <section>
    <?php if ( !empty( $section_title ) ) :?>
      <h2 class="contrast" id="<?php echo sanitize_title ( $section_title ); ?>">
          <?php echo $section_title ?>
      </h2>
    <?php endif; ?>

    <table class="js-hide-empty">
      <thead>
        <tr>
          <th>Name</th>
          <th><?php echo !empty($table_cell_title) ? $table_cell_title : 'Title'; ?></th>
          <th>Email</th>
          <th>Phone</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ( $members as $member ):?>
        <tr>
          <td><?php echo isset( $member['full_name'] ) ? $member['full_name'] : ''; ?></td>
          <td><?php echo isset($member['title'] ) ?  $member['title']: '';?></td>
          <td><?php echo isset( $member['email'] ) ? '<a translate="no" href="mailto:' . $member['email'] .'"> ' .  $member['email']. '</a>' : ''; ?></td>
          <td><?php echo isset( $member['phone'] ) ?
            '<a href="tel:' . $member['phone']['area'] . $member['phone']['phone-co-code'] . $member['phone']['phone-subscriber-number']  . '">(' . $member['phone']['area'] . ') ' . $member['phone']['phone-co-code'] .'-' . $member['phone']['phone-subscriber-number'] . '</a>' : '' ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>

    </table>

    </div>
  </section>
</div>
<!-- /Project Staff members -->
<?php endif; ?>
