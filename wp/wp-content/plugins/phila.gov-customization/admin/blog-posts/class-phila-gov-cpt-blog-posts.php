<?php
/**
 *  Custom Post type for Blog Posts
 *
 */
if ( class_exists( "Phila_Gov_Blog_Posts" ) ){
  $cpt_blog_posts = new Phila_Gov_Blog_Posts();
}


class Phila_Gov_Blog_Posts{

  public function __construct(){

    add_action( 'init', array( $this, 'create_phila_blog_posts' ), 1 );

  }

  function create_phila_blog_posts() {
    register_post_type( 'blog_post',
      array(
        'labels' => array(
          'name' => __( 'Blog posts' ),
          'menu_name' => __('Blog posts'),
          'singular_name' => __( 'Blog post' ),
          'add_new'   => __( 'Add a post' ),
          'all_items'   => __( 'All posts' ),
          'add_new_item' => __( 'Add a blog post' ),
          'edit_item'   => __( 'Edit blog post' ),
          'view_item'   => __( 'View blog post' ),
          'search_items'   => __( 'Search blog post' ),
          'not_found'   => __( 'No Pages Found' ),
          'not_found_in_trash'   => __( 'Blog post not found in trash' ),
        ),
        'taxonomies' => array(
            'category',
            'post_tag'
          ),
          'public' => true,
          'has_archive' => true,
          'show_in_rest' => true,
          'show_in_menu'  => true,
          'rest_base' => 'blog-posts',
          'menu_icon' => 'dashicons-media-document',
          'hierarchical' => false,
          'supports'  => array(
            'title',
            'editor',
            'thumbnail',
            'revisions'
          ),
        'rewrite' => array(
          'slug' => 'posts',
          'with_front' => false,
        ),
      )
    );
  }

}
