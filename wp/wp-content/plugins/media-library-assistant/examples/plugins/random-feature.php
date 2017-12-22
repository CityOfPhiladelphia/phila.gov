<?php
/**
 * Provides two approaches for assigning a random "post thumbnail"/"featured image" to a post
 *
 * When a post is created or updated, an associated taxonomy term is selected and used to pick
 * a random image having the same term. The image is assigned as the "post thumbnail"/"featured image"
 * for the post.
 *
 * @package Random Featured Image
 * @version 1.01
 */

/*
Plugin Name: Random Featured Image
Plugin URI: http://fairtradejudaica.org/media-library-assistant-a-wordpress-plugin/
Description: Assigns a random image as the "post thumbnail"/"featured image".
Author: David Lingren
Version: 1.01
Author URI: http://fairtradejudaica.org/our-story/staff/

Copyright 2015 David Lingren

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You can get a copy of the GNU General Public License by writing to the
	Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA
*/

/**
 * Class Random Featured Image implements two approaches for assigning a random
 * "post thumbnail"/"featured image":
 *
 * 1) A shortcode which takes as input the "ids" parameter from [mla_gallery mla_alt_shortcode=...]
 * 2) Code that parses the [mla_gallery] output to find the ID of the selected image.
 *
 * @package Random Featured Image
 * @since 1.00
 */
class RandomFeaturedImage {
	/**
	 * Select the approach: 1) (true) use the shortcode, 2) (false) parse the gallery
	 */
	const USE_SHORTCODE = false; //true;

	/**
	 * Select the taxonomies, e.g., 'category', 'post_tag', 'attachment_category', 'attachment_tag'
	 */
	const POST_TAXONOMY = 'category';
	const ITEM_TAXONOMY = 'attachment_category';

	/**
	 * Initialization function, similar to __construct()
	 *
	 * Installs an action for the WordPress 'save_post' hook.
	 *
	 * @since 1.00
	 *
	 * @return	void
	 */
	public static function initialize() {
		add_action( 'save_post', 'RandomFeaturedImage::rfi_save_post_action', 10, 3 );
	}

	/**
	 * WordPress Action; called from 'save_post' hook(s).
	 *
	 * Triggered by wp_insert_post and wp_publish_post in wp-includes/post.php
	 *
	 * @since 1.00
	 *
	 * @param	integer	Post ID
	 * @param	object	Post object
	 * @param	boolean Are we updating or creating?
	 *
	 * @return	void
	 */
	public static function rfi_save_post_action( $post_ID, $post, $update ) {
		/*
		 * Only assign a random image if (in this order):
		 * 1) The post has been published (avoiding "auto save" revisions)
		 * 2) The post has one or more terms assigned (but not the "default category")
		 * 3) There is no current Featured Image
		 */
		if ( 'publish' != $post->post_status ) {
			return;
		}

		$the_terms = get_the_terms( $post_ID, self::POST_TAXONOMY );
		if ( empty( $the_terms ) ) {
			return;
		}

		/*
		 * Optional - filter out the default category
		 */
		if ( 'category' == self::POST_TAXONOMY ) {
			$default_category_id= get_option('default_category');

			foreach( $the_terms as $index => $term ) {
				if ( $term->term_id == $default_category_id ) {
					unset( $the_terms[ $index ] );
					break;
				}
			}
		}

		/*
		 * Remove this if you want to assign a new random image each time the post is updated.
		 */
		if ( '' != get_the_post_thumbnail( $post_ID ) ) {
			return;
		}

		/*
		 * Pick the term, e.g. the first value or perhaps a random value
		 */
		$chosen_name = $the_terms[0]->name;

		/*
		 * Find the right [mla_gallery] parameter name for the taxonomy
		 */
		switch ( self::ITEM_TAXONOMY ) {
			case 'category':
				$taxonomy = 'category_name';
				break;
			case 'post_tag':
				$taxonomy = 'tag';
				break;
			default:
				$taxonomy = self::ITEM_TAXONOMY;
		}

		/*
		 * Use a shortcode to finish the job, or parse the image ID our of gallery output
		 */
		if ( self::USE_SHORTCODE ) {
			add_shortcode( 'random_featured_image', 'RandomFeaturedImage::random_featured_image_shortcode' );
			do_shortcode( sprintf( '[mla_gallery %1$s="%2$s" orderby=rand posts_per_page=1 mla_alt_shortcode=random_featured_image rfi_post_id="%3$d"]', $taxonomy, $chosen_name, $post_ID ) );
			remove_shortcode( 'random_featured_image' );
		} else {
			/*
			 * Compose a simple gallery and capture the output
			 */
			$gallery = do_shortcode( sprintf( '[mla_gallery %1$s="%2$s" orderby=rand posts_per_page=1 size=none link=none mla_style=none mla_caption="rfi_image_id={+attachment_ID+}"]', $taxonomy, $chosen_name, $post_ID ) );

			/*
			 * Find the ID of the random image, if there is one,
			 * then set the featured image.
			 */
			if ( preg_match( '/rfi_image_id=(\d+)/', $gallery, $matches ) ) {
				$success = set_post_thumbnail( $post_ID, absint( $matches[1] ) );
			}
		}
	}

	/**
	 * WordPress Shortcode; assigns the Featured Image
	 *
	 * @since 1.00
	 *
	 * @param	array	shortcode parameters; defaults ( 'rfi_post_id' => 0, 'ids' => '' )
	 *
	 * @return	void	echoes error messages, if any
	 */
	public static function random_featured_image_shortcode( $attr ) {
		$default_arguments = array(
			'rfi_post_id' => 0,
			'ids' => '',
		);

		/*
		 * Accept only the attributes we need and supply defaults
		 */
		$arguments = shortcode_atts( $default_arguments, $attr );

		/*
		 * Make sure we have a post ID
		 */
		if ( empty( $arguments['rfi_post_id'] ) ) {
			return '';
		}

		/*
		 * Make sure we have exactly one image ID
		 */
		$ids = ! empty ( $arguments['ids'] ) ? explode( ',', $arguments['ids'] ) : array();
		if ( empty( $ids ) ) {
			return '';
		} else {
			$ids = $ids[0];
		}

		/*
		 * At last! Set the new featured image
		 */
		$success = set_post_thumbnail( absint( $arguments['rfi_post_id'] ), absint( $ids ) );
		return '';
	} //random_featured_image_shortcode
} //RandomFeaturedImage

/*
 * Install the shortcode and/or filters at an early opportunity
 */
add_action('init', 'RandomFeaturedImage::initialize');
?>