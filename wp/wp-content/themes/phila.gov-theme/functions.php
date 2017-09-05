<?php
/**
 * phila-gov functions and definitions
 *
 * @package phila-gov
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */

if ( ! function_exists( 'phila_gov_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
add_action( 'after_setup_theme', 'phila_gov_setup' );

function phila_gov_setup() {

  /*
   * Make theme available for translation.
   * Translations can be filed in the /languages/ directory.
   * If you're building a theme based on phila-gov, use a find and replace
   * to change 'phila-gov' to the name of your theme in all the template files
   */
  load_theme_textdomain( 'phila-gov', get_template_directory() . '/languages' );

  // Add default posts and comments RSS feed links to head.
  add_theme_support( 'automatic-feed-links' );

  /*
   * Enable support for custom background uploads
   *
   * @link https://codex.wordpress.org/Custom_Backgrounds
   */
  add_theme_support( 'custom-background' );

  /*
   * Enable support for Post Thumbnails on posts and pages.
   *
   * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
   */
  add_theme_support( 'post-thumbnails' );

  // Enable cropping on medium images
  update_option( 'medium_crop', true );

  // old default
  add_image_size( 'phila-thumb', 660, 430, true);

  //Staff Directory thumbnails
  add_image_size( 'staff-thumb', 400, 400, true );


  // This theme uses wp_nav_menu() in any number of locations.
  add_action( 'init', 'phila_register_category_menus' );

    function phila_register_category_menus() {

        $phila_menu_cat_args = array(
            'type'                     => 'post',
            'child_of'                 => 0,
            'parent'                   => '',
            'orderby'                  => 'name',
            'order'                    => 'ASC',
            'hide_empty'               => 1,
            'hierarchical'             => 0,
            'taxonomy'                 => 'category',
            'pad_counts'               => false
        );

        $phila_get_menu_cats = get_categories( $phila_menu_cat_args );
        foreach ( $phila_get_menu_cats as $phila_category ) {
            register_nav_menus( array( 'menu-' . $phila_category->term_id => $phila_category->name ) );
        }
    }

  /*
   * Switch default core markup for search form, comment form, and comments
   * to output valid HTML5.
   */
  add_theme_support( 'html5', array(
    'gallery', 'caption',
  ) );

  add_theme_support( 'title-tag' );

}
endif; // phila_gov_setup


/* Get up globals */
$phila_is_minified = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '' : '.min';

add_filter('document_title_separator', 'phila_filter_sep');

function phila_filter_sep(){

  return '|';

}

/* Custom image sizes for responsive images */

add_filter( 'wp_calculate_image_sizes', 'phila_content_image_sizes_attr', 10 , 2 );


function phila_content_image_sizes_attr( $sizes, $size ) {
  $width = $size[0];

  $width && $sizes = '(max-width: 640px) 300px, (max-width: 1024px) 768px, (max-width: 1440px) 1024px,  700px';

  return $sizes;
}

add_filter('pre_get_document_title', 'phila_filter_title');

function phila_filter_title( $title ){
  global $post;
  global $page, $paged;
  if (isset( $post->ID )  ) {
    $page_title = get_the_title( $post->ID );
  }
  $sep = ' | ';
  $site_title = get_bloginfo( 'name' );
  $post_type = get_post_type_object( get_post_type( $post ) );

  $title = array(
    'title' => ''
  );

  // If it's a 404 page, use a "Page not found" title.
  if ( is_404() ) {
    $title['title'] = __( 'Page not found' ) . $sep . $site_title;

  // If on the home or front page, use the site title.
  } elseif ( is_home() && is_front_page() ) {
    $title['title'] = get_bloginfo( 'name', 'display' );

  }elseif ( is_post_type_archive() ){

    if( is_category() ) {

      $cat = get_the_category();
      $title['title'] = post_type_archive_title('', false) . $sep . 'Archive'. $sep . $cat[0]->name . $sep . $site_title;

    }elseif( is_post_type_archive('service_page') ) {
      $title['title'] = 'Service directory' . $sep . $site_title;
    }else{
      $title['title'] = post_type_archive_title('', false) . $sep . 'Archive'. $sep . $site_title;
    }

  } // If on a taxonomy archive, use the term title.
   elseif ( is_tax() ) {

    $tax_name = get_taxonomy( get_query_var( 'taxonomy' ) );
    $title['title'] = single_term_title( '', false ) . $sep . 'Archive' . $sep . $tax_name->labels->name . $sep . $site_title;

    // If on an author archive, use the author's display name.
  } elseif ( is_author() && $author = get_queried_object() ) {

    $title['title'] = $author->display_name . $sep . 'Author Archive'. $sep . $site_title;

  }elseif ( $post_type ) {

    if ($post_type->name == 'page') {
      $title['title'] = $page_title . $sep . $site_title;

    }elseif($post_type->name == 'service_page') {

      $title['title'] = $page_title . $sep . 'Service' . $sep . $site_title;

    }else{

      if( $post_type->name == 'phila_post' || $post_type->name == 'news_post' || $post_type->name == 'press_release' ) {

        $cat = get_the_category();

        $title['title'] = $page_title . $sep . $cat[0]->name . $sep . $post_type->labels->singular_name . $sep . $site_title;

      }else{

        if ( phila_is_department_homepage( $post ) ){

          $title['title'] = $page_title . $sep . 'Homepage' . $sep . $site_title;

        }else{
          $category = get_the_category($post->ID);

          if ( is_array($category) && !empty($category) ) {
            if ( $category[0]->category_parent != 0 ){

              $parent = get_category( $category[0]->category_parent);
              $title['title'] = $page_title  . $sep . $parent->cat_name . $sep . $site_title;

            }else{

              $title['title'] = $page_title  . $sep . $category[0]->name . $sep . $site_title;

            }
          }
        }
      }
    }
  }

  // Add a page number if necessary.
  if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
    $title['page'] = sprintf( __( 'Page %s' ), max( $paged, $page ) );
  }

  $title = implode( "$sep", array_filter( $title ) );

  return $title;
}

add_action('wp_head', 'phila_open_graph', 5);

function phila_open_graph() {
  global $post;
  global $title;

  if( has_post_thumbnail() ){

    $img = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
    $img_src = array_shift( $img );
    $type = 'article';
  }



  $link = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

  //TODO: Determine which twitter account should be used for site attribution
  ?>
  <meta name="twitter:card" content="summary">
  <meta property="og:title" content="<?php echo str_replace(' | ' . get_bloginfo('name'), '', phila_filter_title( $title ) )?>"/>
  <meta property="og:description" content="<?php echo str_replace('"',  '&quot;', phila_get_item_meta_desc()); ?>"/>
  <meta property="og:type" content="<?php echo isset($type) ? $type : 'website' ?>"/>
  <meta property="og:url" content="<?php echo $link ?>"/>
  <meta property="og:site_name" content="<?php echo get_bloginfo(); ?>"/>
  <meta property="og:image" content="<?php echo isset($img_src) ? $img_src : 'http://beta.phila.gov/media/20160715133810/phila-gov.jpg'; ?>"/>
  <?php
}

/**
 * Clean up post titles for social media display
**/

function phila_encode_title( $title ) {
  $title = html_entity_decode( $title );
  $title = urlencode( $title );
  return $title;
}


/**
 * Enqueue scripts and styles.
 */


add_action( 'wp_enqueue_scripts', 'phila_gov_scripts');

function phila_gov_scripts() {
  global $post;
  $post_obj = get_post_type_object( $post->post_type );

  wp_deregister_script( 'jquery' );

  wp_enqueue_style( 'ie-only', get_stylesheet_directory_uri() . '/css/lt-ie-9.css', array( 'standards' )  );

  wp_style_add_data( 'ie-only', 'conditional', 'lt IE 9' );

  wp_enqueue_script( 'phila-scripts', get_stylesheet_directory_uri().'/js/phila-scripts'. $minified . '.js', array(), '0.7.0', true );

  wp_enqueue_style( 'standards', get_stylesheet_directory_uri() . '/css/styles' . $minified . '.css' );

  wp_enqueue_script( 'html5shiv', '//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js', array(), '3.7.3', false);

  wp_script_add_data( 'html5shiv', 'conditional', 'lt IE 9' );

  if  ( is_user_logged_in() ){
    wp_enqueue_script( 'logged-in-js', get_stylesheet_directory_uri() . '/admin/js/front-end.js', array( 'phila-scripts' ), '', true );

    wp_enqueue_style( 'logged-in-css', get_stylesheet_directory_uri() . '/admin/css/front-end.css');
  }

}

add_action('init', 'enqueue_scripts_styles_init');

function enqueue_scripts_styles_init() {
  wp_localize_script( 'ajax-script', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) ); // setting ajaxurl
}

add_action( 'wp_ajax_ajax_action', 'ajax_action_stuff' ); // ajax for logged in users
add_action( 'wp_ajax_nopriv_ajax_action', 'ajax_action_stuff' ); // ajax for not logged in users

function ajax_action_stuff() {
  $post_id = $_POST['post_id']; // getting variables from ajax post
  // doing ajax stuff
  update_post_meta($post_id, 'post_key', 'meta_value');
  echo 'ajax submitted';
  die(); // stop executing script
}

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';
/**
 * Load custom Department menu file.
 */
require get_template_directory() . '/inc/department-menu.php';

/**
 * Load custom Breadcrumb file.
 */
require get_template_directory() . '/inc/breadcrumbs.php';

/**
 * Load custom Utilities file.
 */
require get_template_directory() . '/inc/utilities.php';


// TODO: Remove additional fallback logic (foreach) as when possible
function phila_get_thumbnails(){
  if (has_post_thumbnail()){
    $id = get_post_thumbnail_id();
    $thumbs = array(
      '0' => 'medium',
      '1' => 'phila-thumb',
      '2' => 'home-thumb',
      '3' => 'news-thumb'
    );
    $output = '';
    foreach ($thumbs as $key => $value) {

      $image = wp_get_attachment_image_src($id, $value);

      if ($image[1] == 700 && $image[2] == 400 ) {
        $output .= get_the_post_thumbnail( $post=null, 'medium' );
        break;
      }else if ($image[1] == 660 && $image[2] == 430 ) {
        $output .= get_the_post_thumbnail( $post=null, 'phila-thumb' );
        break;
      }else if ( $image[1] == 550 && $image[2] == 360 ){
        $output .= get_the_post_thumbnail( $post=null, 'home-thumb' );
        break;
      }elseif ( $image[1] == 250 && $image[2] == 165  ) {
        $output .=  get_the_post_thumbnail( $post=null, 'news-thumb' );
        break;
      }
    }
    return $output;
  }
}

function phila_get_department_menu() {
  /*
    Set the menus. We use categories to drive functionality.
    Pass the current category (there should only ever be 1)
    as the menu-id.
  */
  global $post;
  $categories = get_the_category($post->ID);
  if ( ! empty( $categories ) ){
    if ( ! $categories[0]->cat_slug == 'Uncategorized' ){
      $current_cat = $categories[0]->cat_ID;
      $defaults = array(
          'theme_location'  => 'menu-' . $current_cat,
          'menu'            => '',
          'container'       => '',
          'container_class' => '',
          'container_id'    => '',
          'menu_class'      => 'secondary-menu vertical medium-horizontal menu dropdown',
          'menu_id'         => '',
          'echo'            => true,
          'fallback_cb'     => false,//if there is no menu, output nothing
          'before'          => '',
          'after'           => '',
          'items_wrap'      => '
          <div class="row">
            <div class="small-24 columns">
              <div class="mbm-mu" id="site-nav">
                <nav data-swiftype-index="false">
                  <ul id="%1$s" class="%2$s" data-responsive-menu="accordion medium-dropdown">%3$s</ul>
                  </nav>
                </div>
                </div>
              </div>',
          'depth'           => 0,
          'walker'          => new phila_gov_walker_nav_menu
      );
      wp_nav_menu( $defaults );
    }
  }
}

add_filter('nav_menu_css_class', 'phila_add_active_nav_class', 10, 2);

function phila_add_active_nav_class( $classes, $item ){
  if ( in_array( 'current-menu-ancestor', $classes ) ){
    $classes[] = 'current-menu-item';
  }
  return $classes;
}

function phila_get_dept_contact_blocks() {
  $categories = get_the_category();
  $default_category_slug = 'uncategorized';
  $default_category_object = get_category_by_slug($default_category_slug);
  $default_category_id = $default_category_object->term_id;

  $default_sidebar = 'sidebar-' . $default_category_slug .'-' . $default_category_id;

  $current_post_type = get_post_type();

  if ( count($categories) == 1 && $current_post_type == 'department_page' && !is_archive() ) {
    foreach ( $categories as $category ) {
      $cat_slug = $category->slug;
      $cat_id = $category->cat_ID;
      $current_sidebar_name = 'sidebar-' . $cat_slug .'-' . $cat_id;
    }
    echo '<div class="contact">';
    if(is_active_sidebar( $current_sidebar_name )) {
      echo '<div class="row equal-height pvm">';
      dynamic_sidebar( $current_sidebar_name );
      echo '</div>';
    } elseif(is_active_sidebar( $default_sidebar )) {
        echo '<div class="row equal-height pvm">';
        dynamic_sidebar( $default_sidebar );
        echo '</div>';
    }
    echo '</div>';
  } elseif( ( ! count( $categories ) == 1 ) && ( is_active_sidebar( $default_sidebar ) ) && ( $cat_slug == $default_category_slug ) ) {
      echo '<div class="contact">';
      echo '<div class="row equal-height pvm">';
      dynamic_sidebar( $default_sidebar );
      echo '</div>';
      echo '</div>';

  }

}

function phila_get_posted_on(){

  $posted_on_meta['author'] = esc_html( get_the_author() );
  $posted_on_meta['authorURL'] = esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) );
  $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
  $time_string = sprintf( $time_string,
    esc_attr( get_the_date( 'c' ) ),
    esc_html( get_the_date() ),
    esc_attr( get_the_modified_date( 'c' ) ),
    esc_html( get_the_modified_date() )
  );
  $posted_on_meta['time_string'] = $time_string;

  wp_reset_postdata();

  return $posted_on_meta;

}

/**
 * Return an ID of an attachment by searching the database with the file URL.
 */
function phila_get_attachment_id_by_url( $url ) {

  global $wpdb;
  //Filter out everything before /media/ because we are matching on the aws url and not what is in wp-content
  preg_match('/\/media\/(.+)/', $url, $matches);

  $parsed_url = $matches[1];

  $attachment = $wpdb->get_col($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'amazonS3_info' AND meta_value LIKE %s;", '%"' .'media/' .$parsed_url .'"%' ));

  // Returns null if no attachment is found
  return $attachment;
}

function phila_format_document_type($document_type){
  switch($document_type){
    case 'vnd.openxmlformats-officedocument.wordprocessingml.document':
      echo 'docx';
      break;
    case 'pdf':
      echo $document_type;
      break;
    case 'msword':
      echo 'doc';
      break;
    case 'vnd.ms-powerpointtd':
      echo 'ppt';
      break;
    case 'vnd.openxmlformats-officedocument.presentationml.presentation':
      echo 'pptx';
      break;
    case 'vnd.ms-excel':
      echo 'xls';
      break;
    case 'vnd.openxmlformats-officedocument.spreadsheetml.sheet':
      echo 'xlsx';
      break;
    case 'plain':
      echo  'txt';
      break;
  }
}
/**
 * Look up author by slug and use author ID
 *
 * @since 0.22.0
 * @param $query_vars
 * @return $query_vars string Returns new author-nickname var.
 */
add_filter( 'request', 'phila_gov_request' );

function phila_gov_request( $query_vars ){
  if ( array_key_exists( 'author_name', $query_vars ) ) {
    global $wpdb;
    $author_id = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key='nickname' AND meta_value = %s", $query_vars['author_name'] ) );
    if ( $author_id ) {
      $query_vars['author'] = $author_id;
      unset( $query_vars['author_name'] );
    }
  }
  return $query_vars;
}
/**
 * Swaps author user name with author nickname
 *
 * @since 0.22.0
 * @param $link Author link
 * @param $author_id
 * @param $author_name
 * @return $link string Returns modified author link.
 */
add_filter( 'author_link', 'phila_gov_author_link', 10, 3 );

function phila_gov_author_link( $link, $author_id, $author_name ){
  $author_nickname = get_user_meta( $author_id, 'nickname', true );
  if ( $author_nickname ) {
    $link = str_replace( $author_name, $author_nickname, $link );
  }
  return $link;
}

add_filter( 'get_the_archive_title', 'phila_change_post_archive_title' );

function phila_change_post_archive_title(){
  if ( is_post_type_archive( 'document' ) ){
    _e('Publications &amp; forms', 'phila-gov');
    single_cat_title(' | ');
  }elseif ( is_post_type_archive( 'phila_post' ) ){
    _e('Posts', 'phila-gov');
    single_cat_title(' | ');
  }elseif( is_post_type_archive( 'news_post' ) ){
    _e('News &amp; events', 'phila-gov');
    single_cat_title(' | ');
  }elseif( is_post_type_archive( 'press_release' ) ){
    _e('Press releases', 'phila-gov');
    single_cat_title(' | ');
  }elseif( is_tag() ){
    single_tag_title('Tagged in: ');
  }elseif( is_author() ){
    _e('Posts | ', 'phila-gov');
    echo get_the_author();
  }else{
    post_type_archive_title();
  }
}

/**
 *
 * @return $full_department_list_args Array Arguments for use in WP Query to get full list of all departments. Returns a list of department_page IDs that are at the top level or have the homepage metabox checked.
 * @param $category Optional
 */

function phila_get_department_homepage_list(){

  $top_level_department_pages =  array(
    'post_type' => 'department_page',
    'posts_per_page'=> -1,
    'orderby' => 'title',
    'order' => 'asc',
    'post_parent' => 0,
    'fields' => 'ids'
  );

  $marked_homepages = array(
    'post_type' => 'department_page',
    'meta_key' => 'phila_department_home_page',
    'meta_value' => 1,
    'fields' => 'ids'
  );
  $get_top_level_pages = new WP_Query( $top_level_department_pages );
  $get_marked = new WP_Query( $marked_homepages );

  $full_department_homepage_list = array_merge( $get_top_level_pages->posts, $get_marked->posts );

  //remove duplicates
  array_unique( $full_department_homepage_list );

  $full_department_list_args = array(
    'post_type' => 'department_page',
    'post__in' => $full_department_homepage_list,
    'posts_per_page'=> -1,
    'orderby' => 'title',
    'order' => 'asc',
  );

  return $full_department_list_args;
}

/**
 * Adds 'department-home' class to appropriate department homepages or a 'department-landing' class to departments with no bg selected
 *
 * @since 0.23.0
 * @link https://codex.wordpress.org/Function_Reference/body_class
 * @param $classes
 * TODO: change this to reflect our new metadata related to department homepages
 */

add_filter( 'body_class', 'phila_home_classes' );

function phila_home_classes( $classes ) {

  global $post;

  if ( isset($post) ) {

    if ( $post->post_type == 'department_page' ) {

      $parents = get_post_ancestors( $post->ID );

      $post_id = isset( $_GET['post'] ) ? $_GET['post'] : ( isset( $_POST['post_ID'] ) ? $_POST['post_ID'] : false );

      $children = get_pages( array( 'child_of' => $post_id ) );

      //this is a department homepage
      if( ( count( $children ) != 0 ) && ( $post->post_parent == 0 ) ){
        //this class allows us to determine if a department page is just a holder for a site
        $classes[] = 'department-landing';

        if ( has_post_thumbnail( $post->ID ) ) {
          //this classname is important b/c we reference it in scripts.js to determine which header should be displayed (blue or white)
          $classes[] = 'department-home';

        }

      }

    }

  }
    return $classes;
}

/* Returns true if this page is a department page, has children and no other parents - i.e. department homepage
Or, has metadata t
*/

function phila_is_department_homepage( $post ) {

  global $post;

  if ( isset($post) ) {

    if ( $post->post_type == 'department_page' ) {

      $parents = get_post_ancestors( $post->ID );

      $post_id = isset( $_GET['post'] ) ? $_GET['post'] : ( isset( $_POST['post_ID'] ) ? $_POST['post_ID'] : false );

      $children = get_pages( array( 'child_of' => $post_id ) );

      $marked_homepage = rwmb_meta('phila_department_home_page');
      //this is a department homepage
      if( ( ( count( $children ) != 0 ) && ( $post->post_parent == 0 ) ) || ( $marked_homepage == 1) ){

        return true;

      }
    }
  }
}

function phila_get_home_news(){

  $category = get_the_category();
  $contributor = rwmb_meta( 'phila_news_contributor', $args = array( 'type'=>'text') );

  echo '<a href="' . get_permalink() .'" class="card equal">';

  echo phila_get_thumbnails();

  if (function_exists('rwmb_meta')) {

    echo '<div class="content-block">';

    the_title('<h3 class="pvm">', '</h3>');

    the_date('' ,'<span class="small-text">','</span>');

    if ($contributor === ''){
        echo '<span>' . $category[0]->cat_name . '</span>';
    }else {
        echo '<span>' . $contributor . '</span>';
    }

    echo '<p>' . phila_get_item_meta_desc( )  . '</p>';

  }

  echo '</div></a>';
}

/**
 * Echo a title and link to the department currently in the loop. Matches on category and page nice names, which *should* always be the same.
 * TODO: investigate a better way of handling the match.
 * @param $category String or array of categories applied to a page. Required.
 * @param $byline Boolean Include ' by ' in display. Default true. Optional.
 * @param $name_list Boolean Return comma separated list of nice department names. Optional.
 *
 **/

function phila_get_current_department_name( $category, $byline = false, $break_tags = false, $name_list = false ){

  if( !empty( $category ) && $category[0]->slug != 'uncategorized' ) {

    $cat_name = array();
    $cat_ids = array();
    $all_available_pages = array();
    $final_list = array();
    $full_links = array();
    $basename = array();
    $urls = array();
    $names = array();

    foreach( $category as $cat ){
      array_push( $cat_name, $cat->name );
    }
    foreach( $category as $cat ){
      array_push( $cat_ids, $cat->cat_ID );
    }

    $cat_id_string = implode( ', ', $cat_ids );

    $args = phila_get_department_homepage_list();

    $args['category__in'] = $cat_ids;

    $get_links = new WP_Query( $args );

    if ( $get_links->have_posts() ) {

      while ( $get_links->have_posts() ) {

        $get_links->the_post();

        $permalink = get_the_permalink();
        $the_title = get_the_title();

        if ( $permalink != '' ) {

          $all_available_pages[$permalink] = $the_title;

        }
      }
    }

    wp_reset_postdata();

    if ( $byline == true ) {
      echo ' by ';
    }
    //FIXME: Find a better way to identify category/url relationship
    foreach( $all_available_pages as $k=>$v ) {

      $formatted_v = str_replace( "&#8217;", "'", $v );

      foreach ( $cat_name as $name ) {

        if( preg_match("/\b$name\b/i", $formatted_v ) ) {

          $final_list[$k] = $v;

        }
      }
    }

    foreach ( $final_list as $k => $v ){
      $markup = '<a href="' . $k . '">' . $v . '</a>';
      $urls = basename( $k );
      array_push( $basename, $urls );
      array_push( $full_links, $markup );
      array_push( $names, $v );
    }

    if ( $name_list == true ) {
      $name_listed = str_replace( "&#8217;", "'", $names );
      return implode(', ',  $name_listed);
    }

    if ( $break_tags == true ) {
      return implode( '<br>', $full_links );
    }else{
      return implode(', ', $full_links);
    }
  }
}

function phila_get_service_updates(){

    $service_update_details = rwmb_meta( 'service_update' );
    $current_time = current_time('timestamp');
    $valid_update = false;

    $service_date_format = isset( $service_update_details['phila_date_format'] ) ? $service_update_details['phila_date_format'] : '';

    if ( $service_date_format == 'date'):
      $service_effective_start = isset( $service_update_details['phila_effective_start_date'] ) ? $service_update_details['phila_effective_start_date'] : '';
      $service_effective_end = isset( $service_update_details['phila_effective_end_date'] ) ? $service_update_details['phila_effective_end_date'] : '';

      //Add the number of seconds in 24 hours to the base date, which will always be 00:00:00 of the selected day. This ensures the update will remain visible for the duration of the selected day.
      if ( ( intval( $service_effective_start['timestamp'] ) <= $current_time ) && ( intval( $service_effective_end['timestamp'] ) + 86400 ) >= $current_time ) :
        $valid_update = true;
      endif;
    elseif ( $service_date_format == 'datetime') :
      $service_effective_start = isset( $service_update_details['phila_effective_start_datetime'] ) ? $service_update_details['phila_effective_start_datetime'] : '';

      $service_effective_end = isset( $service_update_details['phila_effective_end_datetime'] ) ? $service_update_details['phila_effective_end_datetime'] : '';

      if ( ( intval($service_effective_start['timestamp'] ) <= $current_time ) && ( intval($service_effective_end['timestamp'] ) >= $current_time ) ):
        $valid_update = true;
      endif;
    elseif ($service_date_format == 'none'):
      $service_effective_start = '';
      $service_effective_end = '';
      $valid_update = true;
    endif;

    //Don't set any additional vars unless the update is current
    if ( $valid_update ):
      $service_type = isset( $service_update_details['phila_update_type'] ) ? $service_update_details['phila_update_type'] : '';
      $service_level = isset( $service_update_details['phila_update_level'] ) ? $service_update_details['phila_update_level'] : '';
      $service_message = isset( $service_update_details['phila_service_update_message'] ) ? $service_update_details['phila_service_update_message'] : '';
      $service_link_text = isset( $service_update_details['phila_update_link_text'] ) ? $service_update_details['phila_update_link_text'] : '';
      $service_link = isset( $service_update_details['phila_update_link'] ) ? $service_update_details['phila_update_link'] : '';
      $service_off_site = isset( $service_update_details['phila_off_site'] ) ? $service_update_details['phila_off_site'] : '';

      switch($service_type){
        case 'city':
          $service_icon = 'fa-institution';
          break;
          case 'roads':
            $service_icon = 'fa-road';
            break;
          case 'transit':
            $service_icon = 'fa-subway';
            break;
          case 'trash':
            $service_icon = 'fa-trash';
            break;
          case 'phones':
            $service_icon = 'fa-phone';
            break;
          case 'offices':
            $service_icon = 'fa-building-o';
            break;
          default :
            $service_icon = '';
            break;
      }
      switch($service_level){
        case '0':
          $service_level_label = 'normal';
          break;
          case '1':
            $service_level_label = 'warning';
            break;
          case '2':
            $service_level_label = 'critical';
            break;
          default :
            $service_level_label = 'normal';
            break;
      }

      $output_item ='';

      $output_item = array(
        'service_type' => $service_type,
        'service_icon' => $service_icon,
        'service_level' => $service_level,
        'service_level_label' => $service_level_label,
        'service_message' => $service_message,
        'service_link_text' => $service_link_text,
        'service_link' => $service_link,
        'service_off_site'  => $service_off_site,
        'service_date_format' => $service_date_format,
        'service_effective_start' => $service_effective_start,
        'service_effective_end' => $service_effective_end,
      );

      return $output_item;
    else :
      return;
    endif;
}
/**
 * Returns the meta_desc for an item.
 * @param $bloginfo Boolean. Default true. Determines if bloginfo description should render, or nothing. Typically for use in front-end rendering, as meta description should always have a fallback.
 * @param $post Int. Pass post ID if we are not in the loop.
 *
 **/
function phila_get_item_meta_desc( $bloginfo = true ){
  global $post;

  $meta_desc = array();

  // TODO: Remove all old description fields.
  $dept_desc = rwmb_meta( 'phila_dept_desc' );

  $post_desc = rwmb_meta( 'phila_post_desc' );

  $news_desc = rwmb_meta( 'phila_news_desc' );

  $document_desc = rwmb_meta( 'phila_document_description' );

  $page_desc = rwmb_meta( 'phila_page_desc' );

  $canonical_meta_desc = rwmb_meta( 'phila_meta_desc' );

  //This order matters. If $canonical_meta_desc is found first, it should be used.
  array_push($meta_desc, $canonical_meta_desc, $page_desc, $document_desc, $news_desc, $post_desc, $dept_desc);


  foreach ($meta_desc as $desc){
    if ( !empty( $desc ) ) {
      return wp_strip_all_tags($desc);
    }
  }

  if( is_archive() || is_search() || is_home() ) {
    if ($bloginfo) {
      return bloginfo( 'description' );
    }
  }

  if ( get_post_type() == 'department_page' ) {

    if ( empty( $dept_desc ) && !empty( $post->post_content ) ){

      $dept_desc = $post->post_content;

    }else{
      //fallback if the wysiwyg editor is empty
      $parents = get_post_ancestors( $post->ID );
      $id = ($parents) ? $parents[count($parents)-1]: $post->ID;

      $dept_desc = rwmb_meta( 'phila_dept_desc', $args = array('type' => 'textarea'), $post_id = $id );

    }

    return mb_strimwidth( wp_strip_all_tags($dept_desc), 0, 140, '...');

  //special handing for hierarchical content
  }else if( is_page() ){

    $parents = get_post_ancestors( $post->ID );
    $id = ($parents) ? $parents[count($parents)-1]: $post->ID;

    $page_object = get_page( $post->ID );
    $content = $page_object->post_content;

    $page_desc = rwmb_meta( 'phila_page_desc', $args = array('type' => 'textarea'), $post_id = $id );

    if ( !empty($page_desc) ) {

      return wp_strip_all_tags( $page_desc );

    }else if ( !empty( $content ) ) {

      return mb_strimwidth( wp_strip_all_tags( $content ),  0, 140, '...');

    }else{
      if ($bloginfo) {
        return bloginfo( 'description' );
      }
    }

  }else{
    if ($bloginfo) {
      return bloginfo( 'description' );
    }
  }
}

/**
 * Return a string representing the template currently applied to a page in the loop.
 *
 **/

function phila_get_selected_template( $post_id = null ){

  $user_selected_template = rwmb_meta( 'phila_template_select', $args = array(), $post_id );

  if ( empty( $user_selected_template ) ){
    return get_post_type();
  }

  return $user_selected_template;
}

/**
 * Do the math to determine the correct column span for X items on a 24 column grid.
 *
 * @param $item_count - Numeric string. Required. The count of the items in the grid.
 * @return $column_count The column count
 **/

function phila_grid_column_counter( $item_count ){

  if ( $item_count % 24 == 0 ){
    $column_count = 24 / $item_count;
  }else{
    $column_count = round( 24 / $item_count );
    if ( $item_count >= 5 ) {
      $column_count -= 1;
    }
  }
  return $column_count;
}

function phila_tax_highlight( $info_panel ){
  $output = array();
  if ( !empty($info_panel) ){

    foreach ( $info_panel as $k ){
      $output['callout'] = isset( $info_panel['phila_wysiwyg_callout'] ) ? $info_panel['phila_wysiwyg_callout'] : '';

      $output['due'] = array();

      $output['due']['type'] = isset(
      $info_panel['phila_tax_due_date']['phila_tax_date_choice'] ) ? $info_panel['phila_tax_due_date']['phila_tax_date_choice'] : '';

      if( $output['due']['type'] == 'monthly' || $output['due']['type'] == 'yearly' ){
        $output['due']['date'] = isset( $info_panel['phila_tax_due_date']['phila_tax_date'] ) ?
        $info_panel['phila_tax_due_date']['phila_tax_date'] : '';

      }
      if( $output['due']['type'] == 'yearly' ) {
        $output['due']['month'] = isset( $info_panel['phila_tax_due_date']['phila_tax_date_month'] ) ? $info_panel['phila_tax_due_date']['phila_tax_date_month'] : '' ;
      }

      if( $output['due']['type'] == 'misc' ) {
        $output['due']['misc'] = isset( $info_panel['phila_tax_due_date']['phila_tax_date_misc_details'] ) ? $info_panel['phila_tax_due_date']['phila_tax_date_misc_details'] : '' ;
      }


      $output['due']['summary_brief'] = isset( $info_panel['phila_tax_due_date']['phila_tax_date_summary_brief'] ) ? $info_panel['phila_tax_due_date']['phila_tax_date_summary_brief'] : '';

      $output['due']['summary_detailed'] =  isset( $info_panel['phila_tax_due_date']['phila_tax_date_summary_detailed'] ) ?  $info_panel['phila_tax_due_date']['phila_tax_date_summary_detailed'] : '';

      $output['cost'] = array();

      $output['cost']['number'] = isset( $info_panel['phila_tax_costs']['phila_tax_cost_number'] ) ? $info_panel['phila_tax_costs']['phila_tax_cost_number'] : '';

      $output['cost']['unit'] =  isset( $info_panel['phila_tax_costs']['phila_tax_cost_unit'] ) ?
      $info_panel['phila_tax_costs']['phila_tax_cost_unit'] : '';

      $output['cost']['summary_brief'] =  isset( $info_panel['phila_tax_costs']['phila_tax_cost_summary_brief'] ) ?
      $info_panel['phila_tax_costs']['phila_tax_cost_summary_brief'] : '';

      $output['cost']['summary_detailed'] =  isset( $info_panel['phila_tax_costs']['phila_tax_cost_summary_detailed'] ) ?
      $info_panel['phila_tax_costs']['phila_tax_cost_summary_detailed'] : '';

      $output['code'] = isset( $info_panel['phila_tax_code'] ) ?
      $info_panel['phila_tax_code'] : '';

    }
  }

  return $output;
}

function phila_tax_payment_info( $payment_info ){
  $output = array();

  if ( !empty($payment_info) ) {

    foreach ( $payment_info as $k ){
      $output['who_pays'] = isset($payment_info['phila_tax_who_pays'] ) ? $payment_info['phila_tax_who_pays'] : '';
      $output['late_fees'] = isset($payment_info['phila_tax_late_fees'] ) ? $payment_info['phila_tax_late_fees'] : '';
      $output['discounts'] = isset($payment_info['phila_tax_discounts'] ) ? $payment_info['phila_tax_discounts'] : '';
      $output['exemptions'] = isset($payment_info['phila_tax_exemptions'] ) ? $payment_info['phila_tax_exemptions'] : '';

    }
  }
  return $output;
}

function phila_cta_full_display( $cta_full ){
  $output = array();

  if ( !empty($cta_full) ) {
    foreach ( $cta_full as $k ){

      $output['title'] = isset($cta_full['cta_full_title'] ) ? $cta_full['cta_full_title'] : '';
      $output['description'] = isset($cta_full['cta_full_description'] ) ? $cta_full['cta_full_description'] : '';
      $output['link'] = isset($cta_full['cta_full_link'] ) ? $cta_full['cta_full_link'] : '';
      $output['link_text'] = isset($cta_full['cta_full_link']['link_text'] ) ? $cta_full['cta_full_link']['link_text'] : '';
      $output['url'] = isset($cta_full['cta_full_link']['link_url'] ) ? $cta_full['cta_full_link']['link_url'] : '';
      $output['external'] = isset($cta_full['cta_full_link']['is_external'] ) ? $cta_full['cta_full_link']['is_external'] : '';
      $output['is_survey'] = isset($cta_full['cta_is_survey'] ) ? $cta_full['cta_is_survey'] : '';

      $output['is_modal'] = isset($cta_full['cta_is_modal'] ) ? $cta_full['cta_is_modal'] : '';

      if( !empty($output['is_modal']) ){
        $output['modal_content'] =  isset($cta_full['cta_modal']['cta_modal_content'] ) ? $cta_full['cta_modal']['cta_modal_content'] : '';

        $output['modal_icon'] =  isset($cta_full['cta_modal']['phila_v2_icon'] ) ? $cta_full['cta_modal']['phila_v2_icon'] : '';
      }
    }
  }
  return $output;
}

function phila_extract_clonable_wysiwyg($parent_group){
  $output = array();

  if ( !empty($parent_group) ){

    $clonable_wysiwyg = isset($parent_group['phila_wysiwyg_address_content'] ) ? $parent_group['phila_wysiwyg_address_content'] : $output;

    foreach ( $clonable_wysiwyg as $k => $v ){
      $output[$k] = $v;
    }
  }
  return $output;
}


function phila_extract_stepped_content($parent_group){
  $output = array();

  if ( !empty($parent_group) ){
    $steps = isset($parent_group['phila_ordered_content'] ) ? $parent_group['phila_ordered_content'] : $output;

    foreach ( $steps as $k => $v ){

      $output[$k] = $v;

    }
  }
  return $output;
}

function phila_get_curated_service_list_v2( $service_group ){

  $output = array();

  if ( !empty($service_group) ){

    foreach ( $service_group as $k => $v ){
      $output[$k] = $v;
    }
  }
  return $output;
}

function phila_loop_clonable_metabox( $metabox_name ){

  $output = array();

  if ( !empty($metabox_name) ){

    foreach ( $metabox_name as $k => $v ){
      $output[$k] = $v;
    }
  }
  return $output;
}

function phila_additional_content( $input ){
  $output = array();
  if ( !empty($input) ) {

    foreach ($input as $k => $v) {
      $output['forms'] = isset( $input['phila_forms_instructions']['phila_document_page_picker'] ) ? $input['phila_forms_instructions']['phila_document_page_picker'] : '';

      $output['related'] = isset( $input['phila_related']['phila_related_content'] ) ? $input['phila_related']['phila_related_content'] : '';

      $output['aside']['did_you_know'] = isset( $input['phila_did_you_know']['phila_did_you_know_content'] ) ? $input['phila_did_you_know']['phila_did_you_know_content'] : '';

      $output['aside']['questions'] = isset( $input['phila_questions']['phila_question_content'] ) ? $input['phila_questions']['phila_question_content'] : '';

    }
  }

  return $output;
}

function phila_get_start_process( $input ){
  $output = array();

  if ( !empty($input) ) {

    foreach ($input as $k => $v) {
      $output['content'] = isset( $input['phila_wysiwyg_process_content'] ) ? $input['phila_wysiwyg_process_content'] : '';

      $output['button_text'] = isset( $input['phila_start_button']['link_text'] ) ? $input['phila_start_button']['link_text'] : '';

      $output['button_url'] = isset( $input['phila_start_button']['link_url'] ) ? $input['phila_start_button']['link_url'] : '';

      $output['button_external'] = isset( $input['phila_start_button']['is_external'] ) ? $input['phila_start_button']['is_external'] : '';

    }
  }

  return $output;
}

function phila_connect_panel($connect_panel) {

  $output_array = array();

  foreach ($connect_panel as $key => $value) {

    $output_array['social'] = array();

      if ( isset( $connect_panel['phila_connect_social']['phila_connect_social_facebook'] ) && $connect_panel['phila_connect_social']['phila_connect_social_facebook'] != '') $output_array['social']['facebook'] = $connect_panel['phila_connect_social']['phila_connect_social_facebook'];

      if ( isset( $connect_panel['phila_connect_social']['phila_connect_social_twitter'] ) && $connect_panel['phila_connect_social']['phila_connect_social_twitter'] != ''  ) $output_array['social']['twitter'] = $connect_panel['phila_connect_social']['phila_connect_social_twitter'];

      if ( isset( $connect_panel['phila_connect_social']['phila_connect_social_instagram'] ) && $connect_panel['phila_connect_social']['phila_connect_social_instagram'] != '' ) $output_array['social']['instagram'] = $connect_panel['phila_connect_social']['phila_connect_social_instagram'];

      if ( isset( $connect_panel['phila_connect_social']['phila_connect_social_youtube'] ) && $connect_panel['phila_connect_social']['phila_connect_social_youtube'] != '' ) $output_array['social']['youtube'] = $connect_panel['phila_connect_social']['phila_connect_social_youtube'];

      if ( isset( $connect_panel['phila_connect_social']['phila_connect_social_flickr'] ) && $connect_panel['phila_connect_social']['phila_connect_social_flickr'] != '' ) $output_array['social']['flickr'] = $connect_panel['phila_connect_social']['phila_connect_social_flickr'];

    $output_array['address'] = array(
      'st_1' => isset( $connect_panel['phila_connect_address']['phila_connect_address_st_1'] ) ? $connect_panel['phila_connect_address']['phila_connect_address_st_1'] :'',

      'st_2' => isset( $connect_panel['phila_connect_address']['phila_connect_address_st_2'] ) ? $connect_panel['phila_connect_address']['phila_connect_address_st_2'] :'',

      'city' => isset( $connect_panel['phila_connect_address']['phila_connect_address_city'] ) ? $connect_panel['phila_connect_address']['phila_connect_address_city'] :'Philadelphia',

      'state' => isset( $connect_panel['phila_connect_address']['phila_connect_address_state'] ) ? $connect_panel['phila_connect_address']['phila_connect_address_state'] :'PA',

      'zip' => isset( $connect_panel['phila_connect_address']['phila_connect_address_zip'] ) ? $connect_panel['phila_connect_address']['phila_connect_address_zip'] :'19107',
    );

    $output_array['phone'] = array(
      'area' => isset( $connect_panel['phila_connect_general']['phila_connect_phone']['area'] ) ? $connect_panel['phila_connect_general']['phila_connect_phone']['area'] : '',

      'co-code' => isset( $connect_panel['phila_connect_general']['phila_connect_phone']['phone-co-code'] ) ? $connect_panel['phila_connect_general']['phila_connect_phone']['phone-co-code'] : '',

     'subscriber-number' => isset( $connect_panel['phila_connect_general']['phila_connect_phone']['phone-subscriber-number'] ) ? $connect_panel['phila_connect_general']['phila_connect_phone']['phone-subscriber-number']  : '',
    );

    $output_array['fax'] =
      isset( $connect_panel['phila_connect_general']['phila_connect_fax'] ) && is_array( $connect_panel['phila_connect_general']['phila_connect_fax'] ) ? $connect_panel_fax = '(' . $connect_panel['phila_connect_general']['phila_connect_fax']['area'] . ') ' . $connect_panel['phila_connect_general']['phila_connect_fax']['phone-co-code'] . '-' . $connect_panel['phila_connect_general']['phila_connect_fax']['phone-subscriber-number'] : '' ;

    $output_array['email'] =
      isset( $connect_panel['phila_connect_general']['phila_connect_email'] ) ? $connect_panel['phila_connect_general']['phila_connect_email'] :'';

      $output_array['cta'] = array(

        'title' => isset( $connect_panel['phila_connect_cta']['phila_connect_cta_title'] ) ? $connect_panel['phila_connect_cta']['phila_connect_cta_title'] :'',

        'url' => isset( $connect_panel['phila_connect_cta']['phila_connect_cta_url'] ) ? $connect_panel['phila_connect_cta']['phila_connect_cta_url'] :'',

        'summary' => isset( $connect_panel['phila_connect_cta']['phila_connect_cta_summary'] ) ? $connect_panel['phila_connect_cta']['phila_connect_cta_summary'] :'',

      );

      $output_array['see_all'] =
        isset( $connect_panel['phila_connect_general']['connect_see_all'] ) ? $connect_panel['phila_connect_general']['connect_see_all'] :'';

  }

  if (array_key_exists( 'social' , $output_array )){
    return $output_array;
  } else {
    return;
  }
  // return $connect_panel;
}


function phila_get_page_icon( $post ){

  $icon = rwmb_meta( 'phila_page_icon', $args = array(), $post );

  return $icon;
}

function phila_return_ordinal($num){
  $j = $num % 10;
  $k = $num % 100;
  if ($j == 1 && $k != 11) {
    return 'st';
  }
  if ($j == 2 && $k != 12) {
    return 'nd';
  }
  if ($j == 3 && $k != 13) {
    return 'rd';
  }
  return 'th';
}

function phila_get_hero_header_v2( $post, $mobile = false ){
  if ( $mobile ) {
    $img = rwmb_meta( 'phila_v2_homepage_hero_mobile', $args = array(
      'size'=>'full'
    ), $post );
  }else{
    $img = rwmb_meta( 'phila_v2_homepage_hero', $args = array(
      'size'=>'full'
    ), $post );
  }

  $output = array();

  if ( !empty($img) ){

    foreach ($img as $k){
      $output = $k['full_url'];
    }
    return $output;
  }
}

function phila_get_department_logo_v2( $post ){
  $img = rwmb_meta( 'phila_v2_department_logo', $args = array(
      'size'=>'full'
    ), $post );

    $output = array();

    if ( !empty($img) ){

      foreach ($img as $k){
        $output = $k['full_url'];
      }
      return $output;
    }
}

function phila_get_department_homepage_typography( $parent ){

  $target_phrases = array(
    "City of Philadelphia",
    "Mayor's Commission on",
    "Mayor's Office of",
    "Philadelphia ",
    "Commission on",
    "Board of",
    "Office of the",
    "Office of",
    "Department of",
    "Bureau of",
  );

  $page_title = $parent->post_title;

  while ( list(, $phrase ) = each( $target_phrases ) ) {
    if ( strpos( $page_title, $phrase ) !== false ) {
      $c  = strlen( $phrase );
      $new_title = '<h1><span class="h3 break-after">'  . $phrase . '</span>' . substr( $page_title, $c ) . '</h1>';
      break;
    }else{
      $new_title = '<h1>' . $page_title . '</h1>';
    }
  }

  return $new_title;
}
