<?php

//TODO: Create delete functionality
function delete_translated_post() {
  global $post;

  $endpoint = rwmb_meta( 'phila_translations_deploy_url', array( 'object_type' => 'setting' ), 'phila_settings' );
  $webhook = $endpoint.'delete-page';
  $post_path = substr(substr(str_replace(home_url(),'',get_permalink($post->ID)), 1), 0, -1);
  if(isset( $post->post_type )) {
    $post_type = $post->post_type;
  }
  $data = json_encode(
    array(  'type' => $post_type,
            'path' => $post_path,
            'status' => 'publish'

          ));
  $args = array(
    'method' => 'DELETE',
    'headers'  => array(
      'Content-type: application/json'
    ),
    'body' => $data
);
  return wp_remote_post($webhook, $args);
}

function phila_get_current_post_type() {
  global $post, $typenow, $current_screen;

  if ($post && $post->post_type) return $post->post_type;
    elseif($typenow) return $typenow;
    elseif($current_screen && $current_screen->post_type) return $current_screen->post_type;
    elseif(isset($_REQUEST['post_type'])) return sanitize_key($_REQUEST['post_type']);
  return null;

}

function publish_translated_post($new_status, $old_status, $post) {
  $post_path = substr(substr(str_replace(home_url(), '', get_permalink($post->ID)), 1), 0, -1);
  $endpoint = rwmb_meta('phila_translations_deploy_url', array('object_type' => 'setting'), 'phila_settings');
  $default_billing_code= rwmb_meta('phila_translations_default_billing_code', array('object_type' => 'setting'), 'phila_settings');
  $post_type = phila_get_current_post_type();
  $categories = get_the_category($post->ID);
  $dept_billing_codes = "";
  $send_to_translation = $_POST['phila_send_to_translation'];
  $owner_amount = 0;
  $dept_code_count = 0;

  foreach ($categories as $category) {
    if (empty($category->slug)) {
      continue;
    }
    $owner_amount++;
    $dept_billing_code = get_term_meta($category->term_id, 'phila_department_billing_code', true);

    // Add billing codes if they exist
    if ($dept_billing_code) {
      if (!strpos($dept_billing_codes, $dept_billing_code)) {
        $dept_billing_codes = trim($dept_billing_codes . ',' . $dept_billing_code, ',');
        $dept_code_count = count(explode(',', $dept_billing_codes));
      }
    }
  }

  // Add default billing if a billing code does not exist
  if ($default_billing_code) {
    if ($owner_amount == 0) {
      $dept_billing_codes = $default_billing_code;
    }
    else if ($owner_amount > $dept_code_count && (!strpos($dept_billing_codes, $default_billing_code))) {
      $dept_billing_codes = trim($dept_billing_codes . ',' . $default_billing_code, ',');
    } 
  }

  switch ($post_type) {
    case 'post':
    case 'calendar':
    case 'site_wide_alert':
    case 'longform_content':
      return;
  }

  //example payload: { "page_slug": "services/culture-recreation", "department_code":"1 - ABC" }
  $webhook = $endpoint;
  if ('publish' === $new_status && $send_to_translation == true && $webhook != '') {

    if (isset($post->post_type)) {
      $post_type = $post->post_type;
    }
    $data = json_encode(
      array(
        'page_slug' => $post_path,
        'department_code'  => $dept_billing_codes,
      )
    );
    $args = array(
      'method' => 'POST',
      'headers'  => array(
        'Content-type: application/json',
        'Content-Length: ' . strlen(json_encode($data)),
        'Accept: application/json',
      ),
      'body' => $data,
    );
    return wp_remote_post($webhook, $args);
  }
}

// add_action( 'trashed_post', 'delete_translated_post', 10, 0 );
add_action( 'transition_post_status', 'publish_translated_post', 10, 3 );
?>
