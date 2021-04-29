<?php 
if ( is_user_logged_in() ) {
  if ( !has_post_thumbnail() && (phila_get_selected_template() == 'post' || phila_get_selected_template() == 'translated_post') ) {
    $error_message_title = "Warning: This blog post doesn't have a featured image.";
    $error_messages = [];
    $item1['link'] = '';
    $item1['text'] = '<p>All blog posts must have a featured image according to the <a href="https://standards.phila.gov/docs/content/how-to-write-a-blog.html">phila.gov digital standards</a>.</p>';
    $item2['link'] = '';
    $item2['text'] = "<p>If you don't have an image, you can use one of these <a href='https://drive.google.com/drive/folders/1w7RAaf5LYVH-mozpRbD5hERSt-S3w6Wa'>general use images</a>.</p>";
    array_push($error_messages, $item1);
    array_push($error_messages, $item2);
  }
}