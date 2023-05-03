<?php 

// register custom fields
register_rest_field( 'post', 'archived', array( 'get_callback' => 'get_archive_status' ));
register_rest_field( 'post', 'categories', array( 'get_callback' => 'get_phila_categories' ));
register_rest_field( 'post', 'template', array( 'get_callback' => 'get_phila_template' ));
register_rest_field( 'post', 'tags', array( 'get_callback' => 'get_phila_tags' ));
// register_rest_field( 'post', 'language', array( 'get_callback' => 'get_phila_language' ));

// add filter functionality
add_filter( 'rest_post_query', 'filter_post_by_archived', 10, 2 );
add_filter( 'rest_post_query', 'filter_post_by_featured', 10, 2 );
add_filter( 'rest_post_query', 'filter_post_by_language', 10, 2 );
// register_rest_field( 'post', 'post_search', array( 'get_callback' => 'search_posts') );
// add count of total items in pages


function get_phila_template( $post ) {
  return phila_get_selected_template($post['id'], true, true);
}

function get_archive_status( $post ) {
  return phila_get_archive_status($post['id']);
}

function get_phila_categories ( $post ) {
  return phila_get_the_category($post['id']);
}

function get_phila_tags ( $post ) {
  return get_the_tags($post['id']);
}

// function get_phila_language ( $post ) {
//   $language = rwmb_meta('phila_select_language', '', $post['id']);
//   if ( empty( $language ) ) {
//     $language = 'english';
//   }
//   return $language;
// }

function filter_post_by_archived( $args, $request ) {
  $archived = $request->get_param( 'archived' );

  if ( empty( $archived )) {
      return $args;
  }

  if ( $archived === 'true' ) {
    $archived = 'archive_now';
  } else if ( $archived === 'false' ){
    $archived = 'default';
  }

  $args['meta_query'] = array(
      array(
          'key'     => 'phila_archive_post',
          'value'   => $archived,
          'compare' => '=',
      ),
  );

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

  $args['meta_query'] = array(
      array(
          'key'     => 'phila_is_feature',
          'value'   => $featured,
          'compare' => '=',
      ),
  );

  return $args;
}

function filter_post_by_language( $args, $request ) {
  $lang = $request->get_param( 'language' );

  if ( empty( $lang )) {
      return $args;
  }

  $args['meta_query'] = array(
      array(
          'key'     => 'phila_select_language',
          'value'   => $lang,
          'compare' => '=',
      ),
  );

  return $args;
}


//   function search_posts( $object, $field_name, $request ) {
  //     $args = array(
  //         'post_type' => 'post',
  //         's' => sanitize_text_field( $request['search'] ),
  //     );
  //     $query = new WP_Query( $args );
  //     $results = array();
  //     if ( $query->have_posts() ) {
  //         while ( $query->have_posts() ) {
  //             $query->the_post();
  //             $item = array(
  //                 'id' => get_the_ID(),
  //                 'title' => get_the_title(),
  //                 'url' => get_permalink(),
  //             );
  //             $results[] = $item;
  //         }
  //         wp_reset_postdata();
  //     }
  //     return $results;
  // }


  //   register_rest_route( 'wp/v2', '/search/(?P<query>[a-zA-Z0-9-_]+)/(?P<post_type>[a-zA-Z0-9-_]+)', array(
  //     'methods' => 'GET',
  //     'callback' => 'my_search_callback',
  //     'args' => array(
  //       'query' => array(
  //         'required' => true,
  //         'validate_callback' => function($param, $request, $key) {
  //           return !empty($param);
  //         },
  //       ),
  //       'page' => array(
  //         'default' => 1,
  //         'validate_callback' => function($param, $request, $key) {
  //           return is_numeric($param) && intval($param) > 0;
  //         },
  //       ),
  //       'per_page' => array(
  //         'default' => 10,
  //         'validate_callback' => function($param, $request, $key) {
  //           return is_numeric($param) && intval($param) > 0 && intval($param) <= 100;
  //         },
  //       ),
  //     ),
  //   ) );


// function my_search_callback1( WP_REST_Request $request ) {
//   $query = $request->get_param('query');
//   $post_type = $request->get_param('post_type');
//   $page = $request->get_param('page');
//   $per_page = $request->get_param('per_page');
 
//   $args = array(
//     's' => $query,
//     'post_type' => $post_type,
//     'post_status' => 'publish',
//     'orderby' => 'date',
//     'order' => 'DESC',
//     'posts_per_page' => $per_page,
//     'paged' => $page,
//   );
 
//   $posts = get_posts( $args );
 
//   $response = array(
//     'results' => $posts,
//     'total' => wp_count_posts($post_type)->publish,
//     'pages' => ceil( wp_count_posts($post_type)->publish / $per_page ),
//   );
 
//   return rest_ensure_response( $response );
// }

// add_action( 'rest_api_init', function() {
//   register_rest_route( 'wp/v2', '/search/(?P<query>[a-zA-Z0-9-_]+)/(?P<post_type>[a-zA-Z0-9-_]+)', array(
//     'methods' => 'GET',
//     'callback' => 'my_search_callback',
//     'args' => array(
//       'query' => array(
//         'required' => true,
//         'validate_callback' => function($param, $request, $key) {
//           return !empty($param);
//         },
//       ),
//       'page' => array(
//         'default' => 1,
//         'validate_callback' => function($param, $request, $key) {
//           return is_numeric($param) && intval($param) > 0;
//         },
//       ),
//       'per_page' => array(
//         'default' => 10,
//         'validate_callback' => function($param, $request, $key) {
//           return is_numeric($param) && intval($param) > 0 && intval($param) <= 100;
//         },
//       ),
//     ),
//   ) );
// } );

// function my_search_callback( WP_REST_Request $request ) {
//   $query = $request->get_param('query');
//   $post_type = $request->get_param('post_type');
//   $page = $request->get_param('page');
//   $per_page = $request->get_param('per_page');
 
//   $args = array(
//     's' => $query,
//     'post_type' => $post_type,
//     'post_status' => 'publish',
//     'orderby' => 'date',
//     'order' => 'DESC',
//     'posts_per_page' => $per_page,
//     'paged' => $page,
//   );
 
//   $posts = get_posts( $args );
 
//   $response = array(
//     'results' => $posts,
//     'total' => wp_count_posts($post_type)->publish,
//     'pages' => ceil( wp_count_posts($post_type)->publish / $per_page ),
//   );
 
//   return rest_ensure_response( $response );
// }
