<?php 
if ( is_user_logged_in() && (phila_get_selected_template() == 'post' || phila_get_selected_template() == 'translated_post') ) {
  
  $error_messages = [];

  if ( !has_post_thumbnail()) {
    $error_messages[] = array(
      'title' => "Warning: This blog post doesn't have a featured image.",
      'link' => '',
      'messages' => array(
        '<p>All blog posts must have a featured image according to the <a href="https://standards.phila.gov/docs/content/how-to-write-a-blog.html">phila.gov digital standards</a>.</p>',
        "<p>If you don't have an image, you can use one of these <a href='https://drive.google.com/drive/folders/1w7RAaf5LYVH-mozpRbD5hERSt-S3w6Wa'>general use images</a>.</p>"
      )
    );
  }

  $posted_on_values = phila_get_posted_on();
  if ( empty($posted_on_values['author'])) {
    $error_messages[] = array(
      'title' => "Warning: This blog post doesn't have an author attributed to it.",
      'link' => '',
      'messages' => array(
        '<p>All blog posts must have at least one author attributed to the post.</p>',
        "<p>If there are no additional authors, make sure the 'Exclude default author from list?' checkbox is unchecked.</p>"
      )
    );
  }

  if (has_post_thumbnail()){
    $id = get_post_thumbnail_id();
      $image = wp_get_attachment_image_src($id, "news-thumb");
      $img_height = $image[1]; 
      $img_width = $image[2];

    if($img_height < 1000 || $img_width < 700){
      $error_messages[] = array(
        'title' => "Warning: The featured image doesn't meet the size requirements",
        'link' => '',
        'messages' => array(
          '<p>The featured image of a blog post must conform to the size requirements of 1000px x 700px</p>'
        )
      );
    }
  }
}
