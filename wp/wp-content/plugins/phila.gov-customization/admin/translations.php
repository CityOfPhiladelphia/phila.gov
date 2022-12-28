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

function publish_translated_post($new_status, $old_status, $post) {
  $post_path = substr(substr(str_replace(home_url(),'',get_permalink($post->ID)), 1), 0, -1);
  $endpoint = rwmb_meta( 'phila_translations_deploy_url', array( 'object_type' => 'setting' ), 'phila_settings' );
  $billing_code = rwmb_meta( 'phila_translations_default_billing_code', array( 'object_type' => 'setting' ), 'phila_settings' );

  //TODO: make billing from settings page a fallback - pull data from individual page settings

  $webhook = $endpoint.'handle-page';
  if('publish' === $new_status ) {
    if(isset( $post->post_type )) {
      $post_type = $post->post_type;
    }
    $data = json_encode(
      array(
        'id' => $post->ID,
        'path' => $post_path,
        'status' => 'publish',
        'billing_code'  => $billing_code,
      ));
    $args = array(
      'method' => 'POST',
      'headers'  => array(
        'Content-type: application/json'
      ),
      'body' => $data
    );
    return wp_remote_post($webhook, $args);
  }
}

// add_action( 'trashed_post', 'delete_translated_post', 10, 0 );
add_action( 'transition_post_status', 'publish_translated_post', 10, 3 );
?>