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

  // Current default
  add_image_size( 'phila-thumb', 660, 430, true);

  add_image_size( 'news-thumb', 250, 165, true );

  //This is temporary, until we decide how to handle responsive images more effectively and in what ratios.
  add_image_size( 'home-thumb', 550, 360, true );

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

add_filter('document_title_separator', 'phila_filter_sep');

function phila_filter_sep(){

  return '|';

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

    }else{

      if( $post_type->name == 'phila_post' || $post_type->name == 'news_post' || $post_type->name == 'press_release' ) {

        $cat = get_the_category();

        $title['title'] = $page_title . $sep . $cat[0]->name . $sep . $post_type->labels->singular_name . $sep . $site_title;

      }else{

        if ( phila_is_department_homepage( $post ) ){

          $title['title'] = $page_title . $sep . 'Homepage' . $sep . $site_title;

        }else{
          $category = get_the_category($post->ID);

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

  if( 'department_page' == get_post_type() ){
    $hero_header_image = rwmb_meta( 'phila_hero_header_image', $args = array('type' => 'file_input'));

    if ( empty($hero_header_image) ) {
      $parent_id = get_post_ancestors( $post->ID );

      $hero_header_image = rwmb_meta( 'phila_hero_header_image', $args = array('type' => 'file_input'), $post_id = $parent_id[0]);

      }
      $img_src = $hero_header_image;
    }elseif( has_post_thumbnail() ){

    $img = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );
    $img_src = array_shift( $img );
    $type = 'article';
  }
  $link = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

  //TODO: Determine which twitter account should be used for site attribution
  ?>
  <meta name="twitter:card" content="summary">
  <meta property="og:title" content="<?php echo str_replace(' | ' . get_bloginfo('name'), '', phila_filter_title( $title ) )?>"/>
  <meta property="og:description" content="<?php echo phila_get_item_meta_desc(); ?>"/>
  <meta property="og:type" content="<?php echo isset($type) ? $type : 'website' ?>"/>
  <meta property="og:url" content="<?php echo $link ?>"/>
  <meta property="og:site_name" content="<?php echo get_bloginfo(); ?>"/>
  <meta property="og:image" content="<?php echo isset($img_src) ? $img_src : 'http://alpha.phila.gov/media/20160715133810/phila-gov.jpg'; ?>"/>
  <?php
}


/**
 * Register widget areas for all categories. To appear on department pages.
 *
 * TODO: This could be a scalability issue. More research needs to be done.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
add_action( 'widgets_init', 'phila_gov_widgets_init', 10 );

function phila_gov_widgets_init() {
  $args = array(
    'orderby' => 'name',
    'parent' => 0,
    'hide_empty' => false
    );
  $categories = get_categories( $args );

  foreach ( $categories as $category ) {

    $slug = $category->slug;
    $name = $category->name;
    $cat_id = $category->cat_ID;

    register_sidebar( array(
      'name'          => __( $name . ' Sidebar', 'phila-gov' ),
      'id'            => 'sidebar-' . $slug .'-' . $cat_id,
      'description'   => '',
      'before_widget' => '<aside id="%1$s" class="medium-8 columns widget %2$s center equal">',
      'after_widget'  => '</aside>',
      'before_title'  => '<h1 class="h4 widget-title">',
      'after_title'   => '</h1>',
    ) );
  }
  //only one of these
  register_sidebar( array(
    'name'          => __( 'News Sidebar', 'phila-gov' ),
    'id'            => 'sidebar-news',
    'description'   => '',
    'before_widget' => '<aside id="%1$s" class="widget %2$s">',
    'after_widget'  => '</aside>',
    'before_title'  => '<h1 class="widget-title">',
    'after_title'   => '</h1>',
  ) );
}

/**
 * Enqueue scripts and styles.
 */

add_action( 'wp_enqueue_scripts', 'phila_gov_scripts');

function phila_gov_scripts() {

  wp_enqueue_style( 'pattern_portfolio', '//cityofphiladelphia.github.io/patterns/dist/1.4.1/css/patterns.css' );

  wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css', array(), '4.4.0' );

  wp_enqueue_style( 'ionicons', '//code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css', array(), '2.0.0' );

  wp_enqueue_style( 'theme-styles', get_stylesheet_directory_uri() . '/css/styles.css', array('pattern_portfolio') );

  wp_enqueue_style( 'ie-only', get_stylesheet_directory_uri() . '/css/lt-ie-9.css', array( 'theme-styles' )  );
  wp_style_add_data( 'ie-only', 'conditional', 'lt IE 9' );

  wp_enqueue_script( 'text-filtering', '//cdnjs.cloudflare.com/ajax/libs/list.js/1.1.1/list.min.js', array(), '1.1.1', true );

  wp_enqueue_script( 'foundation-js', '//cdnjs.cloudflare.com/ajax/libs/foundation/6.1.2/foundation.min.js', array('jquery',  'jquery-migrate'), '2.8.3', true );

  wp_enqueue_script( 'pattern-scripts', '//cityofphiladelphia.github.io/patterns/dist/1.4.1/js/patterns.min.js', array('jquery', 'foundation-js'), true );

  wp_enqueue_script( 'phila-scripts', get_stylesheet_directory_uri().'/js/phila-scripts.min.js', array('jquery', 'text-filtering', 'foundation-js', 'pattern-scripts'), 1.0, true );

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
 * Add breadcrumb support
 *
 */
function phila_breadcrumbs() {
  global $post;
  global $output;
  global $i;

  echo '<ul class="breadcrumbs">';
  if ( !is_front_page() ) { //no breadcrumb on the homepage
    echo '<li><a href="';
    echo get_option('home');
    echo '">';
    phila_util_echo_website_url();
    echo '</a></li>';

    if ( is_singular('news_post') ) {

      echo '<li><a href="/news">News</a></li>';
      echo '<li>';
      the_title();
      echo '</li>';

      }elseif ( is_singular('notices') ) {
        $categories = get_the_category($post->ID);

        echo '<li><a href="/notices">Notices</a></li>';
        if ( !$categories == 0 ) {
          echo '<li><a href="/notices/' . $categories[0]->slug . '">'. $categories[0]->name . '</a></li>';
        }
        echo '<li>';
        the_title();
        echo '</li>';

      }elseif ( is_singular('phila_post') ) {

        echo '<li><a href="/posts">Posts</a></li>';
        echo '<li>';
        the_title();
        echo '</li>';

      }elseif ( is_singular('press_release') ) {

        echo '<li><a href="/press-releases">Press Releases</a></li>';
        echo '<li>';
        the_title();
        echo '</li>';

    }elseif ( is_singular('calendar') ) {

      echo '<li>Calendar: ' . get_the_title() . '</li>';

    } elseif ( is_post_type_archive('department_page' ) ) {

        echo '<li>' . __( 'Departments', 'phila.gov' ) . '</li>';

    } elseif ( ( is_post_type_archive('news_post') && is_tax('topics') ) ) {

        echo '<li><a href="/news">News</a></li>';

        echo '<li>'. $term_obj->name . '</li>';

    } elseif ( ( is_post_type_archive('news_post') && is_category() ) ) {

        echo '<li><a href="/news">News</a></li>';
        $category = get_the_category($post->ID);

        echo '<li>' . $category[0]->name . '</li>';

    } elseif ( is_post_type_archive('news_post') ) {

        echo '<li>News</li>';

    }elseif ( is_post_type_archive('phila_post') && is_category() )  {

        echo '<li><a href="/posts">Posts</a></li>';
        $category = get_the_category($post->ID);

        echo '<li>' . $category[0]->name . '</li>';

    } elseif( is_post_type_archive( 'phila_post' ) ) {

        echo '<li>Posts</li>';

    }elseif ( is_post_type_archive('press_release') && is_category() )  {

      echo '<li><a href="/press-releases">Press Releases</a></li>';
      $category = get_the_category($post->ID);

      echo '<li>' . $category[0]->name . '</li>';

    } elseif ( is_post_type_archive('press_release') ) {

      echo '<li>Press Releases</li>';

    } elseif ( ( is_post_type_archive('notices') && is_category() ) ) {

      echo '<li><a href="/notices">Notices</a></li>';

      $category = get_the_category($post->ID);

      echo '<li>' . $category[0]->name . '</li>';

    } elseif ( is_post_type_archive('notices') ) {

      echo '<li>Notices</li>';

    } elseif ( is_singular('department_page') ) {

      $anc = get_post_ancestors( $post->ID );
      $title = get_the_title();

      foreach ( $anc as $ancestor ) {

        $output = '<li><a href="'.get_permalink($ancestor).'" title="'.get_the_title($ancestor).'">'.get_the_title($ancestor).'</a></li> ' .  $output;
      }
      echo $output;
      echo '<li> '.$title.'</li>';

    } elseif ( is_tax('topics') ) {

      //BROWSE
      $taxonomy = 'topics';
      $queried_term = get_query_var($taxonomy);
      $term_obj = get_term_by( 'slug', $queried_term, 'topics');

      $term = get_term_by( 'slug',   $queried_term, 'topics' ); // get current term
      $parent = get_term($term->parent, $taxonomy);

      if ( ! is_wp_error( $parent ) ) :
        echo '<li><a href="/browse/' . $parent->slug . '">' . $parent->name . '</a></li>';
      endif;

      if ( ! is_wp_error( $parent ) ) :
        echo '<li>' . $term_obj->name . '</li>';
      else :
        echo '<li>'. $term_obj->name . '</li>';
      endif;

    } elseif ( is_page() || get_post_type() == 'service_page') {

      if( $post->post_parent ){

        //$anc = array_reverse(get_post_ancestors( $post->ID ));
        $anc = get_post_ancestors( $post->ID );
        $title = get_the_title();
        foreach ( $anc as $ancestor ) {
          $output = '<li><a href="'.get_permalink($ancestor).'" title="'.get_the_title($ancestor).'">'.get_the_title($ancestor).'</a></li> ' .  $output;
        }
        echo $output;
        echo '<li>'.$title.'</li>';

      } else {
          echo '<li>'.get_the_title().'</li>';
      }

    } elseif( is_tag() ){

      echo '<li><a href="/posts">Posts</a></li>';
      echo '<li>';
       '<span>' . single_tag_title( 'Tagged in: ' ) . '</span>';

    } elseif( is_archive() && is_category() ){

      $categories = get_the_category($post->ID);

      echo '<li><a href="/posts">Posts</a></li>';
      if ( !$categories == 0 ) {
        echo '<li>' . $categories[0]->name . '</li>';
      }
    } elseif ( is_author() ) {

      echo '<li><a href="/posts">Posts</a></li>';
      echo '<li>';
        printf( __( 'Author: %s', 'phila-gov' ), '<span class="vcard">' . get_the_author() . '</span>' );
      echo '</li>';

    } elseif ( is_category() ) {

        echo '<li>';
        the_title();
        echo '</li>';
    }

  }//end is front page
  echo '</ul>';
}//end breadcrumbs

/**
 * Utility functions
 */

//this is used throughout the theme and is meant to be updated once the major switch happens
function phila_util_echo_website_url(){
  echo 'alpha.phila.gov';
}

//spits out a nice version of the department category name
function phila_util_get_current_cat_name(){
  $category = get_the_category();
  foreach( $category as $cat){
    return $cat->name;
  }
}

//spits out a nice version of the department category slug
function phila_util_get_current_cat_slug(){
  $category = get_the_category();
  foreach( $category as $cat){
    return $cat->slug;
  }
}

// TODO: Remove additional fallback logic (foreach) as when possible
function phila_get_thumbnails(){
  if (has_post_thumbnail()){
    $id = get_post_thumbnail_id();
    $thumbs = array(
      '0' => 'phila',
      '1' => 'home',
      '2' => 'news'
    );
    $output = '';

    // echo 'Using phila_get_thumb';
    foreach ($thumbs as $key => $value) {
      $image = wp_get_attachment_image_src($id, $value . '-thumb');
      if ($image[1] == 660 && $image[2] == 430 ) {
        $output .= get_the_post_thumbnail( $post=null, 'phila-thumb' );
        break;
      } elseif ( $image[1] == 550 && $image[2] == 360 ) {
        $output .= get_the_post_thumbnail( $post=null, 'home-thumb' );
        break;
      }
      elseif ( $image[1] == 250 && $image[2] == 165  ) {
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
          'menu_class'      => 'department-menu medium-horizontal menu',
          'menu_id'         => '',
          'echo'            => true,
          'fallback_cb'     => false,//if there is no menu, output nothing
          'before'          => '',
          'after'           => '',
          'items_wrap'      => '
          <div class="row department-nav">
            <div class="small-24 columns">
              <div class="title-bar" data-responsive-toggle="site-nav" data-hide-for="medium">
              <button class="menu-icon" type="button" data-toggle><span class="title-bar-title">Menu</span></button>
              </div>
            <div class="top-bar mbm-mu" id="site-nav">
              <nav data-swiftype-index="false">
                <ul id="%1$s" class="%2$s" data-responsive-menu="drilldown medium-dropdown"><li class="menu-item menu-item-type-custom menu-item-object-custom show-for-small-only"><a href="/"><i class="fa fa-angle-left fa-lg" aria-hidden="true"></i> Back to alpha.phila.gov</a></li>%3$s</ul>
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
    if(is_active_sidebar( $current_sidebar_name )) {
      echo '<div class="row equal-height ptm">';
      dynamic_sidebar( $current_sidebar_name );
      echo '</div>';
    } elseif(is_active_sidebar( $default_sidebar )) {
        echo '<div class="row equal-height ptm">';
        dynamic_sidebar( $default_sidebar );
        echo '</div>';
    }
  } elseif( ( ! count( $categories ) == 1 ) && ( is_active_sidebar( $default_sidebar ) ) && ( $cat_slug == $default_category_slug ) ) {
      echo '<div class="row equal-height ptm">';
      dynamic_sidebar( $default_sidebar );
      echo '</div>';
  }

}

function phila_get_posted_on(){

  $posted_on_meta['author'] = esc_html( get_the_author() );
  $posted_on_meta['authorURL'] = esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) );
  $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
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
  if ( is_post_type_archive( 'phila_post' ) ){
    _e('Post Archive', 'phila-gov');
    single_cat_title(' | ');
  }elseif( is_post_type_archive( 'news_post' ) ){
    _e('News Archive', 'phila-gov');
    single_cat_title(' | ');
  }elseif( is_post_type_archive( 'press_release' ) ){
    _e('Press Release Archive', 'phila-gov');
    single_cat_title(' | ');
  }elseif( is_tag() ){
    single_tag_title('Tagged in: ');
  }elseif( is_author() ){
    _e('Author Archive | ', 'phila-gov');
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
 * Find and displays the correct header images
 *
 * @since 0.22.0
 * @link https://codex.wordpress.org/Custom_Backgrounds,
 * @param $classes
 *
 */
add_action('wp_head', 'phila_output_header_images', 100);


function phila_output_header_images(){
  global $post;

  $page_bg_image_url = null;

  if ( is_front_page() ) {
    $page_bg_image_url = get_background_image();

  }elseif( is_404() ) {
    $page_bg_image_url = null;

  }

  $output = "<style type='text/css' id='alpha-custom-page-background'>body.custom-background { background-image: url('" . $page_bg_image_url . "') } </style>";

  echo $output;

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

    the_title('<h3>', '</h3>');

    if ($contributor === ''){
        echo '<span>' . $category[0]->cat_name . '</span>';
    }else {
        echo '<span>' . $contributor . '</span>';
    }

    echo '<p>' . phila_get_item_meta_desc()  . '</p>';

  }

  echo '</div></a>';
}

/**
 * Gets the list of topics available used in:
 * templates/topics-child.php
 * templates/topics-parent.php
 * taxonomy-topics.php
 *
 */
function phila_get_parent_topics(){

  $args = array(
    'orderby' => 'name',
    'fields'=> 'all',
    'parent' => 0,
    'hide_empty'=> true
  );

  $current_term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );

  $terms = get_terms( 'topics', $args );

  if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
    echo '<ul class="tabs vertical">';

    foreach ( $terms as $term ) {

      if (isset($current_term->slug) ) {
        $active = ( $current_term->slug === $term->slug ) ? ' is-active' : '';

      }else {
        $active = '';
      }

      echo '<li class="tabs-title ' . $term->slug . $active . '"><a href="/browse/' . $term->slug . '">' . $term->name . '</a></li>';

    }
    echo '</ul>';
  }
}
/**
 * Utility function to get a list of all topics and their children on the site.
 *
 */
function phila_get_master_topics(){
  $parent_terms = get_terms('topics', array('orderby' => 'slug', 'parent' => 0, 'hide_empty' => 0));
  echo '<ul>';
  foreach($parent_terms as $key => $parent_term) {

    echo '<li><h3>' . $parent_term->name . '</h3>';
    echo  $parent_term->description;

    $child_terms = get_terms('topics', array('orderby' => 'slug', 'parent' => $parent_term->term_id, 'hide_empty' => 0));

    if($child_terms) {
      echo '<ul class="subtopics">';
      foreach($child_terms as $key => $child_term) {
        echo '<li><h4>' . $child_term->name . '</h4>';
        echo  $child_term->description . '</li></li>';
      }

    }
    echo '</ul>';
  }
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

      if ( $name_list == true ) {
        $name_listed = str_replace( "&#8217;", "'", $names );

        return implode(', ',  $name_listed);

      }

    }

    if ( $break_tags == true ) {
      return implode( '<br>', $full_links );
    }else{
      return implode(', ', $full_links);
    }
  }
}

function phila_get_event_content_blocks(){

  $output_array = array();
  $content_blocks = rwmb_meta( 'event_content_blocks' );

  foreach( $content_blocks as $key => $array_value ) {
    $output_item ='';

    $block_title = isset( $array_value['phila_event_block_content_title'] ) ? $array_value['phila_event_block_content_title'] : '';
    $block_link = isset( $array_value['phila_event_block_link'] ) ? $array_value['phila_event_block_link'] : '';
    $block_summary = isset( $array_value['phila_event_block_summary'] ) ? $array_value['phila_event_block_summary'] : '';
    $block_image = isset( $array_value['phila_event_block_image'] ) ? $array_value['phila_event_block_image'] : '';
    $block_image_credit = isset( $array_value['phila_event_block_image_credit'] ) ? $array_value['phila_event_block_image_credit'] : '';


    $output_item = array(
      'block_title' => $block_title,
      'block_summary' => $block_summary,
      'block_link' => $block_link,
      'block_image' => $block_image,
      'block_image_credit' => $block_image_credit,
    );

    array_push($output_array, $output_item);

  }
  if (array_key_exists( 0 , $output_array )){
      return $output_array;
    } else {
      return;
    }
}

function phila_get_service_updates(){

  $output_array = array();
  $service_updates = rwmb_meta( 'service_updates' );

  foreach( $service_updates as $key => $array_value ) {
    $output_item ='';

    $service_type = isset( $array_value['phila_update_type'] ) ? $array_value['phila_update_type'] : '';
    $service_level = isset( $array_value['phila_update_level'] ) ? $array_value['phila_update_level'] : '';
    $service_message = isset( $array_value['phila_service_update_message'] ) ? $array_value['phila_service_update_message'] : '';
    $service_link_text = isset( $array_value['phila_update_link_text'] ) ? $array_value['phila_update_link_text'] : '';
    $service_link = isset( $array_value['phila_update_link'] ) ? $array_value['phila_update_link'] : '';
    $service_effective_date = isset( $array_value['phila_update_effective_date'] ) ? $array_value['phila_update_effective_date'] : '';
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
    }

    $output_item = array(
      'service_type' => $service_type,
      'service_icon' => $service_icon,
      'service_level' => $service_level,
      'service_message' => $service_message,
      'service_link_text' => $service_link_text,
      'service_link' => $service_link,
      'service_effective_date' => $service_effective_date,
    );

    array_push($output_array, $output_item);

  }
  if (array_key_exists( 0 , $output_array )){
    return $output_array;
  } else {
    return;
  }
}

function phila_get_item_meta_desc(){
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
  array_push($meta_desc, $canonical_meta_desc, $page_desc, $document_desc, $news_desc, $post_desc, $dept_desc );

  foreach ($meta_desc as $desc){
    if ( !empty( $desc ) ) {
      return wp_strip_all_tags($desc);
    }
  }

  if( is_archive() || is_search() || is_home() ) {
    return bloginfo( 'description' );
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

    return mb_strimwidth( wp_strip_all_tags($dept_desc), 0, 365, '...');

  //special handing for content collection page types, when appropriate
  }else if( is_page() || get_post_type() == 'service_page' ){

    $parents = get_post_ancestors( $post->ID );
    $id = ($parents) ? $parents[count($parents)-1]: $post->ID;

    $page_object = get_page( $post->ID );
    $content = $page_object->post_content;

    $page_desc = rwmb_meta( 'phila_page_desc', $args = array('type' => 'textarea'), $post_id = $id );

    if ( !empty($page_desc) ) {

      return wp_strip_all_tags( $page_desc );

    }else if ( !empty( $content ) ) {

      return mb_strimwidth( wp_strip_all_tags( $content ),  0, 365, '...');

    }else{
      return bloginfo( 'description' );
    }

  }else{
    return bloginfo( 'description' );
  }
}

/**
 * Return a string representing the template currently applied to a page in the loop.
 *
 **/

function phila_get_selected_template(){

  $user_selected_template = rwmb_meta( 'phila_template_select' );

  return $user_selected_template;
}
/**
 * Do the math to determine the correct column span for X items on a 24 column grid.
 *
 * @param $item_count - Numeric string. Required. The count of the items in the grid.
 * @return $column_count The column count
 **/

function phila_grid_column_counter( $item_count ){

  $column_count = 24 / $item_count;

  return $column_count;

}

function phila_tax_highlight( $info_panel ){
  $output = array();
  if ( !empty($info_panel) ){

    foreach ( $info_panel as $k ){
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

function phila_extract_clonable_wysiwyg($parent_group){
  $output = array();

  if ( !empty($parent_group) ){

    $clonable_wysiwyg = isset($parent_group['phila_cloneable_wysiwyg'] ) ? $parent_group['phila_cloneable_wysiwyg'] : $output;

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


function phila_connect_panel($connect_panel) {

  $output_array = array();

  foreach ($connect_panel as $key => $value) {

    $output_array['social'] = array();

      if ( isset( $connect_panel['phila_connect_social']['phila_connect_social_facebook'] ) && $connect_panel['phila_connect_social']['phila_connect_social_facebook'] != '') $output_array['social']['facebook'] = $connect_panel['phila_connect_social']['phila_connect_social_facebook'];

      if ( isset( $connect_panel['phila_connect_social']['phila_connect_social_twitter'] ) && $connect_panel['phila_connect_social']['phila_connect_social_twitter'] != ''  ) $output_array['social']['twitter'] = $connect_panel['phila_connect_social']['phila_connect_social_twitter'];

      if ( isset( $connect_panel['phila_connect_social']['phila_connect_social_instagram'] ) && $connect_panel['phila_connect_social']['phila_connect_social_instagram'] != '' ) $output_array['social']['instagram'] = $connect_panel['phila_connect_social']['phila_connect_social_instagram'];

    $output_array['address'] = array(
      'st_1' => isset( $connect_panel['phila_connect_address']['phila_connect_address_st_1'] ) ? $connect_panel['phila_connect_address']['phila_connect_address_st_1'] :'',

      'st_2' => isset( $connect_panel['phila_connect_address']['phila_connect_address_st_2'] ) ? $connect_panel['phila_connect_address']['phila_connect_address_st_2'] :'',

      'city' => isset( $connect_panel['phila_connect_address']['phila_connect_address_city'] ) ? $connect_panel['phila_connect_address']['phila_connect_address_city'] :'Philadelphia',

      'state' => isset( $connect_panel['phila_connect_address']['phila_connect_address_state'] ) ? $connect_panel['phila_connect_address']['phila_connect_address_state'] :'PA',

      'zip' => isset( $connect_panel['phila_connect_address']['phila_connect_address_zip'] ) ? $connect_panel['phila_connect_address']['phila_connect_address_zip'] :'19107',
    );

    $output_array['phone'] =
       isset( $connect_panel['phila_connect_general']['phila_connect_phone'] ) && is_array( $connect_panel['phila_connect_general']['phila_connect_phone'] ) ? '(' . $connect_panel['phila_connect_general']['phila_connect_phone']['area'] . ') ' . $connect_panel['phila_connect_general']['phila_connect_phone']['phone-co-code'] . '-' . $connect_panel['phila_connect_general']['phila_connect_phone']['phone-subscriber-number'] :'';

    $output_array['fax'] =
      isset( $connect_panel['phila_connect_general']['phila_connect_fax'] ) && is_array( $connect_panel['phila_connect_general']['phila_connect_fax'] ) ? $connect_panel_fax = '(' . $connect_panel['phila_connect_general']['phila_connect_fax']['area'] . ') ' . $connect_panel['phila_connect_general']['phila_connect_fax']['phone-co-code'] . '-' . $connect_panel['phila_connect_general']['phila_connect_fax']['phone-subscriber-number'] : '' ;

    $output_array['email'] =
      isset( $connect_panel['phila_connect_general']['phila_connect_email'] ) ? $connect_panel['phila_connect_general']['phila_connect_email'] :'';

      $output_array['cta'] = array(

        'title' => isset( $connect_panel['phila_connect_cta']['phila_connect_cta_title'] ) ? $connect_panel['phila_connect_cta']['phila_connect_cta_title'] :'',

        'url' => isset( $connect_panel['phila_connect_cta']['phila_connect_cta_url'] ) ? $connect_panel['phila_connect_cta']['phila_connect_cta_url'] :'',

        'summary' => isset( $connect_panel['phila_connect_cta']['phila_connect_cta_summary'] ) ? $connect_panel['phila_connect_cta']['phila_connect_cta_summary'] :'',

      );

  }

  if (array_key_exists( 'social' , $output_array )){
    return $output_array;
  } else {
    return;
  }
  // return $connect_panel;
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
