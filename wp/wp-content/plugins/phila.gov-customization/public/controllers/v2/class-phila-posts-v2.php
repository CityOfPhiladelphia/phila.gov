<?php 

// register custom fields
register_rest_field( 'post', 'archived',        array( 'get_callback' => 'get_archive_status' ));
register_rest_field( 'post', 'categories',      array( 'get_callback' => 'get_phila_categories' ));
register_rest_field( 'post', 'template',        array( 'get_callback' => 'get_phila_template' ));
register_rest_field( 'post', 'tags',            array( 'get_callback' => 'get_phila_tags' ));
register_rest_field( 'post', 'featured_media',  array( 'get_callback' => 'get_phila_featured_media' ));

add_filter( 'rest_post_query', 'filter_post_by_archived', 10, 2 );
add_filter( 'rest_post_query', 'filter_post_by_featured', 10, 2 );
add_filter( 'rest_post_query', 'filter_post_by_language', 10, 2 );
add_filter( 'rest_post_query', 'filter_post_by_template', 10, 2 );

function get_phila_template( $post ) {
  return phila_get_selected_template($post['id'], true, true);
}

function get_archive_status( $post ) {
  var_dump(phila_get_archive_status($post['id']));
  return phila_get_archive_status($post['id']);
}

function get_phila_categories ( $post ) {
  return phila_get_the_category($post['id']);
}

function get_phila_tags ( $post ) {
  return get_the_tags($post['id']);
}

function get_phila_featured_media ( $post ) {
  $featured_image_id = get_post_thumbnail_id($post['id']);
  if ( $featured_image_id !== 0) {
    $medium_featured_image_url = wp_get_attachment_image_src($featured_image_id, 'medium')[0];
    return $medium_featured_image_url;
  }
  return null;

}




function filter_post_by_archived($args, $request) {

  $archived = $request->get_param('archived');

  $two_years_ago = date('Y-m-d\TH:i:s', strtotime('-2 years'));
  // $now = current_time('timestamp');
  // $archived = filter_var($archived, FILTER_VALIDATE_BOOLEAN);

  // if ($archived == 'true') {
  //   $args['meta_query'][] = array(
  //     // 'relation' => 'OR',
  //     // array(
  //     //     'key'     => 'phila_archive_post',
  //     //     'value'   => 'archive_now',
  //     //     'compare' => '=',
  //     // ),
  //     // array(
  //     //   'relation' => 'AND',
  //     //   array(
  //     //       'key'     => 'phila_archive_post',
  //     //       'value'   => 'default',
  //     //       'compare' => '=',
  //     //   ),
  //       // array(
  //       //   'before' => $two_years_ago,
  //       //   'inclusive' => true,
  //       // ),
  //       // array(
  //       //   'key'     => 'modified',
  //       //   'value'   =>  $two_years_ago,
  //       //   'compare' => '<',
  //       //   'type'    => 'DATETIME',
  //       // )
  //     // ),
  //     // array(
  //     //   'key'     => 'phila_archive_post',
  //     //   'value'   => '',
  //     //   'compare' => '=',
  //     // ),
  //   );
  // } else if ($archived == 'false') {
  //    $args['meta_query'][] = array(
  //     'relation' => 'OR',
  //     array(
  //         'key'     => 'phila_archive_post',
  //         'value'   => 'do_not_archive',
  //         'compare' => '=',
  //     ),
  //     array(
  //       'relation' => 'AND',
  //       array(
  //           'key'     => 'phila_archive_post',
  //           'value'   => 'default',
  //           'compare' => '=',
  //       ),
  //       array(
  //         'after' => $two_years_ago,
  //         'inclusive' => true,
  //       ),
  //     ),
  //   );
  // }

  if ($archived == 'true') {
    $args['date_query'][] = array(
      'before' => $two_years_ago,
      'inclusive' => true,
    );
  }

  // var_dump($args);
  return $args;
}

function filter_post_by_featured( $args, $request ) {
  $featured = $request->get_param( 'featured' );

  if ( empty( $featured )) {
      return $args;
  }

  if ( $featured == 'true') {
    $featured = 1;
  } else if ( $featured == 'false' ){
    $featured = 0;
  }

  $args['meta_query'][] = array(
    'key'     => 'phila_is_feature',
    'value'   => $featured,
    'compare' => '=',
  );

  return $args;
}

function filter_post_by_language( $args, $request ) {
  $lang = $request->get_param( 'language' );

  if ( empty( $lang )) {
      return $args;
  }

  $args['meta_query'][] = array(
    'key'     => 'phila_select_language',
    'value'   => $lang,
    'compare' => '=',
  );

  return $args;
}

//add filter for template
function filter_post_by_template($args, $request) {
  $template = $request->get_param( 'template' );

  if ( empty( $template )) {
      return $args;
  }

  $args['meta_query'][] = array(
    'key'     => 'phila_template_select',
    'value'   => $template,
    'compare' => '=',
  );

  return $args;
}

// function filter_post_by_archived($args, $request){
//   $archived = $request->get_param('archived');

//   if ( empty( $archived )) {
//     return $args;
// }

// $archived = filter_var($archived, FILTER_VALIDATE_BOOLEAN);

// $args['meta_query'][] = array(
//   'key'     => 'is_archived',
//   'value'   => $archived,
//   'compare' => '=',
//   'type'    => 'BOOLEAN',
// );

// return $args;
// }