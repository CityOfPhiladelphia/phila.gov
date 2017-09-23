<?php
/**
 * Add custom breadcrumb support
 *
 */
function phila_breadcrumbs() {
  global $post;
  global $output;
  global $i;

    echo '<ul class="breadcrumbs">';
    echo '<li><a href="';
    echo get_option('home');
    echo '">';
    echo '<i class="fa fa-home" aria-hidden="true"></i><span class="accessible">Home</span>';
    echo '</a></li>';

    if ( is_singular('post') ){
      echo '<li><a href="/the-latest">The latest</a></li>';
      echo '<li>';
      the_title();
      echo '</li>';

    } elseif ( is_singular('news_post') ) {
      $categories = get_the_category($post->ID);

      echo '<li><a href="/news">News &amp; events</a></li>';
      if ( !$categories == 0 ) {
        echo '<li><a href="/news/' . $categories[0]->slug . '">'. $categories[0]->name . '</a></li>';
      }
      echo '<li>';
      the_title();
      echo '</li>';

    } elseif ( is_singular('document') ) {

        echo '<li><a href="/documents">Publications &amp; forms</a></li>';
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

    }elseif ( is_singular('event_page') ) {

      echo '<li>';
      the_title();
      echo '</li>';

    }elseif ( is_singular('press_release') ) {
      $categories = get_the_category($post->ID);

      echo '<li><a href="/press-releases">Press releases</a></li>';
      if ( !$categories == 0 ) {
        echo '<li><a href="/press-releases/' . $categories[0]->slug . '">'. $categories[0]->name . '</a></li>';
      }
      echo '<li>';
      the_title();
      echo '</li>';

    }elseif ( is_singular('calendar') ) {

      echo '<li>Calendar: ' . get_the_title() . '</li>';

    } elseif ( is_post_type_archive('department_page' ) ) {

        echo '<li>' . __( 'City government directory', 'phila.gov' ) . '</li>';

    } elseif ( is_post_type_archive('service_page' ) ) {

        echo '<li>' . __( 'Service directory', 'phila.gov' ) . '</li>';

    } elseif ( ( is_post_type_archive('document') && is_category() ) ) {

        echo '<li><a href="/news">Publications &amp; forms</a></li>';
        $category = get_the_category($post->ID);

        echo '<li>' . $category[0]->name . '</li>';

    } elseif ( is_post_type_archive('document') ) {

      echo '<li>Publications &amp; forms</li>';

    } elseif ( ( is_post_type_archive('news_post') && is_category() ) ) {

        echo '<li><a href="/news">News &amp; events</a></li>';
        $category = get_the_category($post->ID);

        echo '<li>' . $category[0]->name . '</li>';

    } elseif ( is_post_type_archive('news_post') ) {

      echo '<li>News &amp; events</li>';

    } elseif ( is_post_type_archive('phila_post') && is_category() )  {

        echo '<li><a href="/posts">Posts</a></li>';
        $category = get_the_category($post->ID);

        echo '<li>' . $category[0]->name . '</li>';

    } elseif( is_post_type_archive( 'phila_post' ) ) {

        echo '<li>Posts</li>';

    }elseif ( is_post_type_archive('press_release') && is_category() )  {

      echo '<li><a href="/press-releases">Press releases</a></li>';
      $category = get_the_category($post->ID);

      echo '<li>' . $category[0]->name . '</li>';

    } elseif ( is_post_type_archive('press_release') ) {

      echo '<li>Press releases</li>';

    } elseif ( ( is_post_type_archive('notices') && is_category() ) ) {

      echo '<li><a href="/notices">Notices</a></li>';

      $category = get_the_category($post->ID);

      echo '<li>' . $category[0]->name . '</li>';

    } elseif ( is_singular('department_page') ) {

      $anc = get_post_ancestors( $post->ID );
      $title = get_the_title();

      foreach ( $anc as $ancestor ) {

        $output = '<li><a href="'.get_permalink($ancestor).'" title="'.get_the_title($ancestor).'">'.get_the_title($ancestor).'</a></li> ' .  $output;
      }
      echo $output;
      echo '<li> '.$title.'</li>';

    } elseif ( is_page() || get_post_type() == 'service_page') {

      if ( get_post_type() == 'service_page') {
        echo '<li><a href="/services">' . __( 'Services', 'phila.gov' ) . '</a></li>';
      }

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
  echo '</ul>';
}//end breadcrumbs
