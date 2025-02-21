<?php $page_rows = rwmb_meta('phila_row'); ?>
<div class = "medium-16 medium-centered align-middle">
<?php
foreach ($page_rows as $page_row) {
  if ($page_row['phila_adv_posts_options']['phila_adv_posts_select_options'] == 'phila_lists') {
    $list = $page_row['phila_adv_posts_options']['phila_adv_lists'];
    include(locate_template('partials/phila_blog_adv_lists.php'));
  } elseif ($page_row['phila_adv_posts_options']['phila_adv_posts_select_options'] == 'phila_qna') {
    $qna = $page_row['phila_adv_posts_options']['phila_adv_qna'];
    include(locate_template('partials/phila_blog_adv_qa.php'));
  } elseif ($page_row['phila_adv_posts_options']['phila_adv_posts_select_options'] == 'phila_timeline') {
    $timeline_page = $page_row['phila_adv_posts_options']['phila_adv_timeline'];
    include(locate_template('partials/timeline_stub.php'));
  } elseif ($page_row['phila_adv_posts_options']['phila_adv_posts_select_options'] == 'phila_adv_stepped_process') {
    $current_row = $page_row['phila_adv_posts_options'];
    $current_row_option = 'phila_adv_posts_stepped_process';
    include(locate_template('partials/posts/action_guide_v2/components/stepped-content-wrapper.php'));
  } elseif ($page_row['phila_adv_posts_options']['phila_adv_posts_select_options'] == 'phila_image_gallery') {
    $images = $page_row['phila_adv_posts_options']['phila_adv_posts_image_gallery']['phila_image_gallery'];
    $title = $page_row['phila_adv_posts_options']['phila_adv_posts_image_gallery']['phila_image_gallery_details']['phila_image_gallery_title'];
    $description = $page_row['phila_adv_posts_options']['phila_adv_posts_image_gallery']['phila_image_gallery_details']['phila_image_gallery_description']; 
    include(locate_template('partials/phila_blog_adv_image_gallery.php'));
  } elseif ($page_row['phila_adv_posts_options']['phila_adv_posts_select_options'] == 'phila_text_component') {
    $text = $page_row['phila_adv_posts_options']['phila_adv_posts_text_component'];
    include(locate_template('partials/phila_text_component.php'));
  }
} ?>
</div>