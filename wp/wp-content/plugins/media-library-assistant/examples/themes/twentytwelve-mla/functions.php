<?php
/**
 * MLA Child for Twenty Twelve functions and definitions
 *
 * Sets up the theme and provides some helper functions, which are used
 * in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * @package Media Library Assistant
 * @subpackage MLA_Child_Theme
 * @version 1.01
 * @since MLA 1.80
 */

/*
 * Remove the link rel='prev' and link rel='next' tags in the wp_head();
 * this prevents a couple of "query_posts" calls.
 * The other remove_action statements have been suggested in various support topics.
 */
if ( ! is_admin() ) {
    //remove_action( 'wp_head', 'feed_links', 2 ); // Display the links to the general feeds: Post and Comment Feed
	//remove_action( 'wp_head', 'feed_links_extra', 3 ); // Display the links to the extra feeds such as category feeds
	//remove_action( 'wp_head', 'rsd_link' ); // Display the link to the Really Simple Discovery service endpoint, EditURI link
	//remove_action( 'wp_head', 'wlwmanifest_link' ); // Display the link to the Windows Live Writer manifest file.
	remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 ); // Display relational links for the posts adjacent to the current post. OBSOLETE?
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 ); // Display relational links for the posts adjacent to the current post.
	//remove_action( 'wp_head', 'wp_generator' ); // Display the XHTML generator that is generated on the wp_head hook, WP version
	//remove_action( 'wp_head', 'rel_canonical' ); // Display the canonical link fo a singular page
}

/**
 * Loads the text domain(s) for the mla-child-theme, from the WordPress language directory
 * and/or from the theme's own directory.
 *
 * @return void 
 */
function mla_after_setup_theme_action() {
	$domain = 'mla-child-theme';

	load_theme_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain );
	load_theme_textdomain( $domain, get_stylesheet_directory() . '/languages' );

	//the third call is made in the parent theme twentytwelve_setup() function
	//load_theme_textdomain( $domain, get_template_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'mla_after_setup_theme_action' );

/**
 * Customize the <title> tag content for the Tag Gallery and Single Image pages
 *
 * @param string The default page title
 * @param string $sep How to separate the various items within the page title
 * @param string $seplocation Optional. Direction to display title, 'right'.
 *
 * @return string updated title value
 */
function mla_wp_title_filter( $title, $sep, $seplocation ) {
	$sep = " {$sep} ";

	if ( is_page() ) {
		$page = single_post_title( '', false );

		/*
		 * Match specific page titles and replace the default, page title,
		 * with more interesting term or file information.
		 */
		if ( 'Tag Gallery' == $page ) {
			$taxonomy = isset( $_REQUEST['my_taxonomy'] ) ? $_REQUEST['my_taxonomy'] : NULL;
			$slug = isset( $_REQUEST['my_term'] ) ? $_REQUEST['my_term'] : NULL;
			if ( $taxonomy && $slug ) {
				$term = get_term_by( 'slug', $slug, $taxonomy );
				return $term->name . $sep;
			}
		} elseif ( 'Single Image' == $page ) {
			$post_id = isset( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : 0;
			if ( $post_id ) {
				$file = get_attached_file( $post_id );
				$pathinfo = pathinfo( $file );
				return $pathinfo['basename'] . $sep;
			}
		}
	} // is_page

	return $title;
}
add_filter( 'wp_title', 'mla_wp_title_filter', 10, 3 );

/**
 * Generate a taxonomy- and term-specific [mla_gallery]
 *
 * @param array Attributes of the function: taxonomy, term
 *
 * @return void echoes HTML <h3>, <p> and <a> tags
 */
function mla_tag_gallery( $attr = NULL ) {
	/*
	 * Make sure $attr is an array, even if it's empty
	 */
	if ( empty( $attr ) ) {
		$attr = array();
	} elseif ( is_string( $attr ) ) {
		$attr = shortcode_parse_atts( $attr );
	}

	/*
	 * Create the PHP variables we need
	 */
	extract( shortcode_atts( array(
		'taxonomy' => 'attachment_tag',
		'term' => ''
	), $attr ) );

	/*
	 * Convert to objects for validation and labels
	 */
	$taxonomy = get_taxonomy( $taxonomy );
	$term = get_term_by( 'slug', $term, $taxonomy->name );

	if ( empty( $taxonomy ) ) {
		$output = __( 'Taxonomy does not exist.', 'mla-child-theme' );
	}
	elseif ( empty( $term ) ) {
		$output = __( 'Term does not exist.', 'mla-child-theme' );
	}
	else {
		/* translators: 1: term name, 2: taxonomy label */
		$output = '<h3>' . sprintf( __( 'Gallery for term "%1$s" in taxonomy "%2$s"', 'mla-child-theme' ), $term->name, $taxonomy->labels->name ) . '</h3>';
		$output .= '<p>' . do_shortcode( sprintf( '[mla_gallery %1$s="%2$s" post_mime_type=all mla_nolink_text="No items found" ]', $taxonomy->name, $term->slug ) . "</p>\r\n" );
	} // ! empty

	echo $output;
}

/**
 * Generate a taxonomy- and term-specific [mla_gallery], limited by current_page and posts_per_page
 *
 * This function uses $wpdb functions for efficiency.
 *
 * @param array Attributes of the function: page, taxonomy, term, post_mime_type, posts_per_page, current_page
 *
 * @return integer number of posts matching taxonomy & term, before LIMIT. echoes HTML <h3>, <p> and <a> tags
 */
function mla_paginated_term_gallery( $attr = NULL ) {
	global $wpdb;

	/*
	 * Make sure $attr is an array, even if it's empty
	 */
	if ( empty( $attr ) ) {
		$attr = array();
	} elseif ( is_string( $attr ) ) {
		$attr = shortcode_parse_atts( $attr );
	}

	/*
	 * Create the PHP variables we need
	 */
	extract( shortcode_atts( array(
		'page' => NULL,
		'taxonomy' => 'attachment_tag',
		'term' => '',
		'post_mime_type' => 'all',
		'posts_per_page' =>10,
		'current_page' => 1
	), $attr ) );

	/*
	 * Convert to objects for validation and labels
	 */
	$taxonomy = get_taxonomy( $taxonomy );
	$term = get_term_by( 'slug', $term, $taxonomy->name );

	if ( empty( $taxonomy ) ) {
		echo __( 'Taxonomy does not exist.', 'mla-child-theme' );
		return;
	}
	elseif ( empty( $term ) ) {
		echo __( 'Term does not exist.', 'mla-child-theme' );
		return;
	}

	$offset = absint( $current_page - 1 ) * $posts_per_page;

	if ( 'all' == strtolower( $post_mime_type ) ) {
		$count = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $wpdb->term_relationships . ' WHERE ( term_taxonomy_id = ' . $term->term_taxonomy_id . ' )' );
		$posts = implode( ',', $wpdb->get_col( 'SELECT object_id FROM ' . $wpdb->term_relationships . ' WHERE ( term_taxonomy_id = ' . $term->term_taxonomy_id . ' ) LIMIT ' . $offset . ', ' . $posts_per_page ) );
	} else {
		/*
		 * $posts contains all post types, so we further limit the results to select attachments of the
		 * desired MIME type, then apply the limit criteria.
		 */
		$mime_where = wp_post_mime_type_where( $post_mime_type, 'p' );

		$posts = implode( ',', $wpdb->get_col( 'SELECT object_id FROM ' . $wpdb->term_relationships . ' WHERE ( term_taxonomy_id = ' . $term->term_taxonomy_id . ' )' ) );
		$count = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $wpdb->posts . ' AS p WHERE ( p.ID IN ( ' . $posts . ' )' . $mime_where . ')' );
		$posts = implode( ',', $wpdb->get_col( 'SELECT p.ID FROM ' . $wpdb->posts . ' as p WHERE ( p.ID IN ( ' . $posts . ' )' . $mime_where . ') LIMIT ' . $offset . ', ' . $posts_per_page ) );
	}

	$href = empty( $page ) ? '{+link_url+}' : "{+site_url+}{$page}";
	/* translators: 1: term name, 2: taxonomy label */
	$output = '<h3>' . sprintf( __( 'Gallery for term "%1$s" in taxonomy "%2$s"', 'mla-child-theme' ), $term->name, $taxonomy->labels->name ) . '</h3>';
	$output .= '<p>' . do_shortcode( sprintf( '[mla_gallery ids="%1$s" post_mime_type="%2$s" mla_paginate_current=1 mla_nolink_text="No items found" update_post_term_cache="false" mla_link_href="%3$s?post_id={+attachment_ID+}"]', $posts, $post_mime_type, $href ) . "</p>\r\n" );

	echo $output;
	return $count;
}

/**
 * Generate a list of taxonomy- and term-specific links to the page of your choice,
 * listing the terms assigned to a specific post or Media Library item.
 *
 * @param integer ID of the post/page to generate terms for
 * @param array Attributes of the function: site_url, page_url, taxonomy
 *
 * @return void echoes HTML <h3>, <p> and <a> tags
 */
function mla_custom_terms_list( $ID, $attr = NULL ) {
	/*
	 * Make sure $attr is an array, even if it's empty
	 */
	if ( empty( $attr ) ) {
		$attr = array();
	} elseif ( is_string( $attr ) ) {
		$attr = shortcode_parse_atts( $attr );
	}

	/*
	 * Create the three PHP variables we need
	 */
	extract( shortcode_atts( array(
		'site_url' => site_url(),
		'page_path' => '/tag-gallery/',
		'taxonomy' => 'attachment_tag',
	), $attr ) );

	/*
	 * Get the terms associated with the current attachment.
	 * Return nothing if there are no terms associated with the attachment.
	 */
	$terms = get_the_terms( $ID, $taxonomy );
	if ( empty( $terms ) ) {
		return '';
	}

	/* translators: 1: taxonomy slug */
	$output = '<h3>' . sprintf( __( 'Terms list for taxonomy: %1$s', 'mla-child-theme' ), $taxonomy ) . '</h3>';
	/* translators: 1: term name */
	$title = sprintf( __( 'Gallery for %1$s', 'mla-child-theme' ), $taxonomy );
	foreach ( $terms as $term ) {
		$output .= '<p>' . sprintf( '<a href=%1$s%2$s?my_taxonomy=%3$s&my_term=%4$s title="%5$s">%6$s</a>', $site_url, $page_path, $taxonomy, $term->slug, $title, $term->name ) . "</p>\n";
	}// foreach term

	echo $output;
}

/**
 * Generate a list of taxonomy- and term-specific links to the page of your choice.
 * Best used with the Collapse-O-Matic plugin, which uses the [expand] shortcode to
 * display an "accordian-style" list.
 *
 * @param array Attributes of the function: taxonomy
 *
 * @return string HTML <h3>, <p> and <a> tags
 */
function mla_taxonomy_terms_list( $attr = NULL ) {
	/*
	 * Make sure $attr is an array, even if it's empty
	 */
	if ( empty( $attr ) ) {
		$attr = array();
	} elseif ( is_string( $attr ) ) {
		$attr = shortcode_parse_atts( $attr );
	}

	/*
	 * Create the PHP variables we need
	 */
	extract( shortcode_atts( array(
		'taxonomy' => 'attachment_tag',
	), $attr ) );

	$terms = get_terms( $taxonomy );
	if ( empty( $terms ) ) {
		return __( 'There are no non-empty taxonomy values', 'mla-child-theme' );
	}

	/* translators: 1: taxonomy slug */
	$output = '<h3>' . sprintf( __( 'Terms list for taxonomy: %1$s', 'mla-child-theme' ), $taxonomy ) . '</h3>';
	foreach ( $terms as $term ) {
		$output .= '<p>' . do_shortcode( sprintf( '[expand title="%1$s" tag="div" trigclass="level-2 third" targclass="level-2-targ"][mla_gallery %2$s="%3$s" post_mime_type=all mla_nolink_text="%4$s" size=icon mla_style=table mla_markup=table columns=1][/expand]', $term->name, $taxonomy, $term->slug, __( 'No items found', 'mla-child-theme' ) ) . "</p>\r\n" );
	}// foreach term

	return $output;
}

/**
 * Insert thumbnail image tags for Facebook, Twitter, etc.
 *
 * @return void echoes HTML <meta> tags
 */
function mla_insert_social_tags() {
	if ( is_page() ) {
		global $post;
		if ( empty( $post->post_content ) ) {
			return;
		}

		$count = preg_match( '/\[mla_gallery.*attachment_category="([^\"]*)\"/', $post->post_content, $mla_matches );
		if ( $count ) {
			$matched_category = $mla_matches[1]; // for preg_match
			$gallery = do_shortcode( sprintf( '[mla_gallery %1$s="%2$s" size=full link=none mla_style=none posts_per_page=5]', 'attachment_category', $matched_category ) );
			$count = preg_match_all( '/src=\"([^\"]*)\"/', $gallery, $mla_matches );
			if ( $count ) {
				foreach ( $mla_matches[1] as $match ) {
					echo sprintf( '<meta property="og:image" content="%1$s" />', $match ) . "\n";
				}

				echo sprintf( '<meta name="twitter:image:src" content="%1$s" />', $mla_matches[1][0] ) . "\n";
			}

			return;
		} // found mla_gallery

		$count = preg_match( '/\[gallery.*ids="([^\"]*)\"/', $post->post_content, $mla_matches );
		if ( $count ) {
			$matched_posts = $mla_matches[1]; // for preg_match
			$gallery = do_shortcode( sprintf( '[mla_gallery %1$s="%2$s" size=full link=none mla_style=none posts_per_page=5]', 'ids', $matched_posts ) );
			$count = preg_match_all( '/src=\"([^\"]*)\"/', $gallery, $mla_matches );
			if ( $count ) {
				foreach ( $mla_matches[1] as $match ) {
					echo sprintf( '<meta property="og:image" content="%1$s" />', $match ) . "\n";
				}

				echo sprintf( '<meta name="twitter:image:src" content="%1$s" />', $mla_matches[1][0] ) . "\n";
			}

		}
	} // found gallery
}
?>