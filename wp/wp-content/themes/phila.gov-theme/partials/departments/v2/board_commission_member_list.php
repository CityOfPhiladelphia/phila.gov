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
    <div class="accordion commissions" data-accordion data-multi-expand="true" data-allow-all-closed="true">
      <?php foreach ($members as $member):?>
          <!-- Accordion tab title -->
          <?php if( empty($member['headshot']) && empty($member['bio']) && empty($member['email']) && empty($member['phone']) ) : ?>
            <div class="disabled accordion-title"><?php echo isset( $member['full_name']) ? $member['full_name'] : ''; ?><?php echo isset($member['title']) ? ', <i>' . $member['title'] . '</i>': '';?></div>
          <?php else :?>
            <div class="accordion-item" data-accordion-item>

              <a href="#" class="accordion-title"><?php echo isset( $member['full_name']) ? $member['full_name'] : ''; ?><?php echo isset($member['title']) ? ', <i>' . $member['title'] . '</i>': '';?></a>
          <?php endif; ?>
            <div class="accordion-content group" data-tab-content>
              <?php if( isset($member['headshot'])) : ?>
                <?php $image = wp_get_attachment_image_src($member['headshot'][0], $size = 'full');
                echo isset($member['headshot']) ? '<img src="' . $image[0] . '" alt="' . $member['full_name'] .'" class="float-left" width="200" height="200">'  : ''; ?>
              <?php endif?>
              <?php echo isset($member['bio']) ? apply_filters('the_content', $member['bio']) : ''?>
              <?php echo isset( $member['email'] ) ? '<a href="mailto:' . $member['email'] .'"> ' .  $member['email']. '</a>' : ''
              ?>
              <?php if (isset($member['email']) == true && isset($member['phone']) == true) echo ' | ' ?>
              <?php echo isset($member['phone']) ?
              '<a href="tel:' . $member['phone']['area'] . $member['phone']['phone-co-code'] . $member['phone']['phone-subscriber-number']  . '">(' . $member['phone']['area'] . ') ' . $member['phone']['phone-co-code'] .'-' . $member['phone']['phone-subscriber-number'] . '</a>' : ''?>

          </div>
        </div>
      <?php endforeach;?>
    </div>
  </section>
</div>
<?php endif; ?>
