<?php $ann_cats = rwmb_meta('phila_get_ann_cats');
  $ann_cat_override = isset($ann_cats['phila_announcement_category']) ? $ann_cats['phila_announcement_category'] : '';
  $ann_tag_override = isset($ann_override['ann_tag']) ? $ann_override['ann_tag'] : ''; ?>

<!-- Announcement Content-->
<section class="announcements">
  <?php include(locate_template('partials/global/phila_full_row_announcements.php')); ?>
</section>
<!-- /Announcement Content-->
