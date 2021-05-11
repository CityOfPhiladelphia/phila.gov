<?php 
function delete_microsite_post() {
  global $post;
  if(isset( $post->post_type )) {
    $post_type = $post->post_type;
    if ($post->post_type == 'department_page' ) {
        $post_type = 'departments';
    } else if ($post->post_type == 'service_page' ) {
        $post_type = 'services';
    } else if ($post->post_type == 'post' ) {
      $post_type = 'posts';
    }
  }
  $webhook = 'http://philagov-microsite-elb-2-45eebdab64197e1b.elb.us-east-1.amazonaws.com:3000/build-collection/'.$post_type.'/'.sanitize_title( $post->post_name ? $post->post_name : $post->post_title, $post->ID );
  $args = array(
    'method' => 'DELETE',
    'headers'  => array(
        'Content-type: application/json;charset=utf-8',
        'Accept: application/json',
    ),
    'body' => array(
    )
  );
  $http = _wp_http_get_object();
  $http->post( $webhook, $args );
}

function delete_microsite_post_private() {
  global $post;
  if(isset( $post->post_type )) {
    $post_type = $post->post_type;
    if ($post->post_type == 'department_page' ) {
        $post_type = 'departments';
    } else if ($post->post_type == 'service_page' ) {
        $post_type = 'services';
    } else if ($post->post_type == 'post' ) {
      $post_type = 'posts';
    }
  }
  $webhook = 'http://local-phila-gov-microsite-7a2919e31e74066e.elb.us-east-1.amazonaws.com:3000/build-collection/'.$post_type.'/'.sanitize_title( $post->post_name ? $post->post_name : $post->post_title, $post->ID );
  $args = array(
    'method' => 'DELETE',
    'headers'  => array(
        'Content-type: application/json;charset=utf-8',
        'Accept: application/json',
    ),
    'body' => array(
    )
  );
  $http = _wp_http_get_object();
  $http->post( $webhook, $args );
}

function publish_microsite_post($new_status, $old_status, $post) {
  if('publish' === $new_status ) {
    if(isset( $post->post_type )) {
      $post_type = $post->post_type;
      if ($post->post_type == 'department_page' ) {
          $post_type = 'departments';
      } else if ($post->post_type == 'service_page' ) {
          $post_type = 'services';
      } else if ($post->post_type == 'post' ) {
        $post_type = 'posts';
      }
    }
    $webhook = 'http://philagov-microsite-elb-2-45eebdab64197e1b.elb.us-east-1.amazonaws.com:3000/build-collection/'.$post->ID.'/'.$post_type.'/'.sanitize_title( $post->post_name ? $post->post_name : $post->post_title, $post->ID );
    $args = array(
        'method' => 'POST',
        'headers'  => array(
            'Content-type: application/json;charset=utf-8',
            'Accept: application/json',
        ),
        'body' => array(
        )
    );
    $http = _wp_http_get_object();
    var_dump($webhook);
    return $http->post( $webhook, $args );
  }
}

function publish_microsite_post_private($new_status, $old_status, $post) {
  if('publish' === $new_status ||  'private' === $new_status) {
    if(isset( $post->post_type )) {
      $post_type = $post->post_type;
      if ($post->post_type == 'department_page' ) {
          $post_type = 'departments';
      } else if ($post->post_type == 'service_page' ) {
          $post_type = 'services';
      } else if ($post->post_type == 'post' ) {
        $post_type = 'posts';
      }
    }
    $webhook = 'http://local-phila-gov-microsite-7a2919e31e74066e.elb.us-east-1.amazonaws.com:3000/build-collection/'.$post->ID.'/'.$post_type.'/'.sanitize_title( $post->post_name ? $post->post_name : $post->post_title, $post->ID );
    $args = array(
        'method' => 'POST',
        'headers'  => array(
            'Content-type: application/json;charset=utf-8',
            'Accept: application/json',
        ),
        'body' => array(
        )
    );
    $http = _wp_http_get_object();
    return $http->post( $webhook, $args );
  }
}

add_action( 'trashed_post', 'delete_microsite_post', 10, 0 );
add_action( 'trashed_post', 'delete_microsite_post_private', 10, 0 );
add_action( 'transition_post_status', 'publish_microsite_post', 10, 3 );
add_action( 'transition_post_status', 'publish_microsite_post_private', 10, 3 );

?>