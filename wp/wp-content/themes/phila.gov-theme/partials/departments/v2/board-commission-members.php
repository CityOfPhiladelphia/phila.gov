<?php
  /* Partial for board or commission member accordion-style rendering. */
  $section_title = rwmb_meta('section_title');
  $members = rwmb_meta('phila_commission_members');
 ?>

<?php if ( !empty($members)) : ?>
<div class="grid-container">
  <section>
    <?php if (!empty($section_title)) :?>
      <h2 class="contrast"><?php echo $section_title ?></h2>
    <?php endif; ?>
    <div class="accordion" data-accordion>
      <?php foreach ($members as $member):?>
        <li class="accordion-item" data-accordion-item data-multi-expand="true">
          <!-- Accordion tab title -->
          <a href="#" class="accordion-title"><?php echo isset( $member['full_name']) ? $member['full_name'] : ''; ?> <?php echo isset($member['title']) ? '<i>' . $member['title'] . '</i>': '';?></a>

          <div class="accordion-content" data-tab-content>
            <?php $image = wp_get_attachment_image_src($member['headshot'][0], $size = 'full'); ?>
            <?php echo isset($member['headshot']) ? '<img src="' . $image[0] . '" alt="' . $member['full_name'] .'" class="float-left" width="200" height="200">'  : ''; ?>

            <?php echo isset($member['bio']) ? apply_filters('the_content', $member['bio']) . '<br>'  : ''?>
            <?php echo isset( $member['email'] ) ? '<a href="mailto:' . $member['email'] .'"> ' .  $member['email']. '</a>' : ''
            ?>
            <?php if ($member['email'] != '' && $member['phone'] !=
            '' ? ' | ' : '') ?>
            <?php echo isset($member['phone']) ?
            '(' . $member['phone']['area'] . ') ' . $member['phone']['phone-co-code'] .'-' . $member['phone']['phone-subscriber-number']  : ''?>

          </div>
        </li>

      <?php endforeach;?>
    </div>
  </section>
</div>
<?php endif; ?>
