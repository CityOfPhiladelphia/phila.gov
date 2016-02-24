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

  add_image_size( 'news-thumb', 250, 165, true );

  //This is temporary, until we decide how to handle responsive images more effectively and in what ratios.
  add_image_size( 'home-thumb', 550, 360, true );

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
      $title['title'] = post_type_archive_title('', false) . $sep . $cat[0]->name . $sep . $site_title;

    }else{
      $title['title'] = post_type_archive_title('', false) . $sep . $site_title;
    }

  } // If on a taxonomy archive, use the term title.
   elseif ( is_tax() ) {

    $tax_name = get_taxonomy( get_query_var( 'taxonomy' ) );
    $title['title'] = single_term_title( '', false ) . $sep . $tax_name->labels->name . $sep . $site_title;

  }elseif ( $post_type ) {

    if ($post_type->name == 'page') {
      $title['title'] = $page_title . $sep . $site_title;

    }else{

      if( $post_type->name == 'phila_post' || $post_type->name == 'news_post' ) {

        $cat = get_the_category();
        $title['title'] = $page_title . $sep . $cat[0]->name . $sep . $post_type->labels->singular_name . $sep . $site_title;

      }else{

        $title['title'] = $page_title . $sep . $post_type->labels->singular_name . $sep . $site_title;
      }
    }

    // If on an author archive, use the author's display name.
  } elseif ( is_author() && $author = get_queried_object() ) {

    $title['title'] = $author->display_name . $sep . $site_title;

  }

  // Add a page number if necessary.
  if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
    $title['page'] = sprintf( __( 'Page %s' ), max( $paged, $page ) );
  }

  $title = implode( "$sep", array_filter( $title ) );

  return $title;
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
    'parent' => 0
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

  wp_enqueue_style( 'pattern_portfolio', '//cityofphiladelphia.github.io/patterns/dist/1.1.1/css/patterns.css' );

  wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css', array(), '4.4.0' );

  wp_enqueue_style( 'ionicons', '//code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css', array(), '2.0.0' );

  wp_enqueue_style( 'theme-styles', get_stylesheet_directory_uri() . '/css/styles.css', array('pattern_portfolio') );

  wp_deregister_script( 'jquery' );

  wp_deregister_script( 'jquery-migrate' );

  wp_enqueue_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js', false, null, true );

  wp_enqueue_script( 'jquery-migrate', ( '//cdnjs.cloudflare.com/ajax/libs/jquery-migrate/1.2.1/jquery-migrate.min.js' ), array('jquery'), null, true );


  wp_enqueue_script( 'text-filtering', '//cdnjs.cloudflare.com/ajax/libs/list.js/1.1.1/list.min.js', array(), '1.1.1', true );

  wp_enqueue_script( 'foundation-js', '//cdnjs.cloudflare.com/ajax/libs/foundation/6.1.2/foundation.min.js', array('jquery',  'jquery-migrate'), '2.8.3', true );

  wp_enqueue_script( 'pattern-scripts', '//cityofphiladelphia.github.io/patterns/dist/1.1.1/js/patterns.min.js', array('jquery', 'foundation-js'), true );

  wp_enqueue_script( 'phila-scripts', get_stylesheet_directory_uri().'/js/phila-scripts.min.js', array('jquery', 'text-filtering', 'foundation-js'), 1.0, true );
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
  if ( !is_front_page() ) { //display breadcrumbs everywhere but on the homepage
    echo '<li><a href="';
    echo get_option('home');
    echo '">';
    phila_util_echo_website_url();
    echo '</a></li>';

    if ( is_singular('news_post') ) {
      $categories = get_the_category($post->ID);

      echo '<li><a href="/news">News</a></li>';
      if ( !$categories == 0 ) {
        echo '<li><a href="/news/' . $categories[0]->slug . '">'. $categories[0]->name . '</a></li>';
      }

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
        $categories = get_the_category($post->ID);

        echo '<li><a href="/posts">Posts</a></li>';
        if ( !$categories == 0 ) {
          echo '<li><a href="/posts/' . $categories[0]->slug . '">'. $categories[0]->name . '</a></li>';
        }

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

    } elseif ( ( is_post_type_archive('notices') && is_category() ) ) {

      echo '<li><a href="/notices">Notices</a></li>';

      $category = get_the_category($post->ID);

      echo '<li>' . $category[0]->name . '</li>';

    } elseif ( is_post_type_archive('notices') ) {

      echo '<li>Notices</li>';

    } elseif ( is_singular('site_wide_alert') ) {

      echo '<li>';
      the_title();
      echo '</li>';

    } elseif ( is_singular('department_page') ) {

      $anc = get_post_ancestors( $post->ID );
      $title = get_the_title();

      foreach ( $anc as $ancestor ) {

        $output = '<li><a href="'.get_permalink($ancestor).'" title="'.get_the_title($ancestor).'">'.get_the_title($ancestor).'</a></li> ' .  $output;
      }
      echo $output;
      echo '<li> '.$title.'</li>';

    } elseif ( is_singular('service_post') || is_single() ){

        echo '<li>';
        the_title();
        echo '</li>';

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

    } elseif ( is_page() ) {

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

function phila_util_echo_feedback_url(){
    echo 'https://alpha.phila.gov/feedback?url=' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] . '&dept=';
}

function phila_still_migrating_content(){
    echo '<p>We\'re still working on this page\'s design and content. ';
    echo '<a href="';
    echo phila_util_echo_feedback_url();
    echo '">How can we make it better?</a></p>';
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
            <div class="small-24 columns medium-center">
              <div class="title-bar" data-responsive-toggle="site-nav" data-hide-for="medium">
              <button class="menu-icon" type="button" data-toggle><div class="title-bar-title">Menu</div></button>
              </div>
            <div class="top-bar" id="site-nav">
              <nav data-swiftype-index="false">
                <ul id="%1$s" class="%2$s" data-responsive-menu="drilldown medium-dropdown">%3$s</ul>
              </nav>
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

  if ( count($categories) == 1 ) {
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
  } elseif( ( ! count( $categories ) == 1 ) && ( is_active_sidebar( $default_sidebar ) ) ) {
      echo '<div class="row equal-height ptm">';
      dynamic_sidebar( $default_sidebar );
      echo '</div>';
  }

}

function phila_get_posted_on(){
  global $post;

  $author = esc_html( get_the_author() );
  $authorURL = esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) );
  $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
  $time_string = sprintf( $time_string,
    esc_attr( get_the_date( 'c' ) ),
    esc_html( get_the_date() ),
    esc_attr( get_the_modified_date( 'c' ) ),
    esc_html( get_the_modified_date() )
  );
  $current_category = get_the_category();

  if ( !$current_category == '' ) {
    $department_page_args = array(
      'post_type' => 'department_page',
      'tax_query' => array(
        array(
          'taxonomy' => 'category',
          'field'    => 'slug',
          'terms'    => $current_category[0]->slug,
        ),
      ),
      'post_parent' => 0,
      'posts_per_page' => 1,
    );
    $get_department_link = new WP_Query( $department_page_args );
    if ( $get_department_link->have_posts() ) {
      while ( $get_department_link->have_posts() ) {
        $get_department_link->the_post();
        // //$current_cat_slug = $current_category[0]->slug;
      }
    }
  }

  $current_cat_slug = $current_category[0]->slug;
  $dept_cat_permalink = get_the_permalink();
  $dept_title = get_the_title();

  wp_reset_postdata();

  if ( ( $post->post_type == 'phila_post') && ( $current_cat_slug != 'uncategorized' ) ){
    echo '<div class="posted-on row column pvs">';
    if ( has_post_thumbnail() ){
      echo '<div class="columns hide-for-small-only medium-24">';
      $large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
      the_post_thumbnail( 'news-thumb' );
      echo '</div>';
    }
    echo '<div class="byline small-24 medium-24 column pvs"><div class="float-left center prs icon hide-for-small-only"><span class="fa-stack fa-lg">
  <i class="fa fa-circle fa-stack-2x"></i>
  <i class="fa fa-pencil fa-stack-1x fa-inverse"></i>
</span></div><div class="details small-text">';
    echo '<span>Posted by <a href="' . $authorURL . '">' . $author . '</a></span><br>';
    // NOTE: the id and data-slug are important. Google Tag Manager
    // uses it to attach the department to our web analytics.
    echo '<span><a href="' . $dept_cat_permalink . '" id="content-modified-department"
          data-slug="' . $current_cat_slug . '">' . $dept_title . '</a></span><br>';
    echo '<span>' . $time_string . '</span></div></div>';
  }
  elseif ( ( $post->post_type == 'news_post') && ( $current_cat_slug != 'uncategorized' ) ){
    echo '<span class="small-text">' . $time_string . ' by <a href="' . $dept_cat_permalink . '" id="content-modified-department"
          data-slug="' . $current_cat_slug . '">' . $dept_title . '</a></span>';
  }
}


function phila_get_full_page_title(){
  global $post;
  $page_path = '';
  $page_title = get_the_title( $post );
  $page_path .= $page_title;
  $anc = get_post_ancestors( $post->ID );

  foreach ( $anc as $ancestor ) {
    $page_path .= ' | ' . get_the_title($ancestor);
  }
  $page_path .= ' | ' . get_bloginfo('name');

  return $page_path;
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

add_filter( 'get_the_archive_title', 'change_post_archive_title' );

function change_post_archive_title(){
  if ( is_post_type_archive( 'phila_post' ) ){
    _e('Posts', 'phila-gov');
  }elseif( is_category() && is_post_type_archive( 'phila_post' ) ){
    _e('Posts | ', 'phila-gov');
    single_cat_title();
  }elseif( is_author() ){
    _e('Author | ', 'phila-gov');
    echo get_the_author();
  }
}

add_filter( 'get_the_archive_title', 'phila_filter_h1' );

function phila_filter_h1( $title ) {

  if ( is_tag() ) {

    $title = single_tag_title( 'Tag | ', false );

  } elseif ( is_category() ){

    $title = single_cat_title( ' | ', false );

  }

  return $title;

}
/**
 * Filter department page archive to use list template & show all Parent Pages
 *
 * @since 0.22.0
 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/pre_get_posts
 * @param $query
 *
 */

add_action( 'pre_get_posts', 'phila_department_list' );

function phila_department_list( $query ) {
  if ( is_admin() ){
    return;
  }
  if ( is_post_type_archive('department_page')
         && ! empty( $query->query['post_type']  == 'department_page' ) ) {

    $query->set('posts_per_page', -1);
    $query->set('orderby', 'title');
    $query->set('order', 'asc');
    $query->set( 'post_parent', 0 );

  }
}

/**
 * Find and displays the correct header imagesy
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
 * Adds 'department-home' class to appropiate department homepages or a 'department-landing' class to departments with no bg selected
 *
 * @since 0.23.0
 * @link https://codex.wordpress.org/Function_Reference/body_class
 * @param $classes
 *
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

function phila_get_home_news(){
  $category = get_the_category();
  $contributor = rwmb_meta( 'phila_news_contributor', $args = array( 'type'=>'text') );

  $desc = rwmb_meta( 'phila_news_desc', $args = array( 'type'=>'textarea' ) );

  echo '<a href="' . get_permalink() .'" class="card equal">';

  the_post_thumbnail( 'home-thumb'  );

  if (function_exists('rwmb_meta')) {

    echo '<div class="content-block">';

    the_title('<h3>', '</h3>');

    if ($contributor === ''){
        echo '<span>' . $category[0]->cat_name . '</span>';
    }else {
        echo '<span>' . $contributor . '</span>';
    }

    echo '<p>' . $desc  . '</p>';

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
