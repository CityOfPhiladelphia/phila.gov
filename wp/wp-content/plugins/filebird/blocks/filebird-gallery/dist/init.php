<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
function filebird_gallery_block_assets() {
	wp_register_script(
		'filebird_gallery-block-js',
		NJFB_PLUGIN_URL . 'blocks/filebird-gallery/dist/blocks.build.js',
		array( 'wp-blocks', 'wp-i18n', 'wp-element' ),
		NJFB_VERSION,
		true
	);

	wp_register_style(
		'filebird_gallery-block-css',
		NJFB_PLUGIN_URL . 'blocks/filebird-gallery/dist/blocks.style.build.css',
		array(),
		NJFB_VERSION
	);

	register_block_type(
		'filebird/block-filebird-gallery',
		array(
			'editor_script'   => 'filebird_gallery-block-js',
			'render_callback' => 'filebird_gallery_render',
			'editor_style'    => 'filebird_gallery-block-css',
			'attributes'      => array(
				'selectedFolder' => array(
					'type'    => 'array',
					'default' => array(),
				),
				'hasCaption'     => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'captions'       => array(
					'type'    => 'object',
					'default' => array(),
				),
				'imagesRemoved'  => array(
					'type'    => 'array',
					'default' => array(),
				),
				'images'         => array(
					'type'    => 'array',
					'default' => array(),
				),
				'columns'        => array(
					'type'    => 'integer',
					'default' => 3,
				),
				'isCropped'      => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'linkTo'         => array(
					'type'    => 'string',
					'default' => 'none',
				),
				'sortBy'         => array(
					'type'    => 'string',
					'default' => 'date',
				),
				'sortType'       => array(
					'type'    => 'string',
					'default' => 'DESC',
				),
			),
		)
	);

	wp_set_script_translations( 'filebird_gallery-block-js', 'filebird', NJFB_PLUGIN_PATH . 'i18n/languages/' );
}

function filebird_gallery_prepare_ids( $ids ) {
	return array_map(
		function( $item ) {
			return (int) $item;
		},
		$ids
	);
}

function filebird_gallery_render( $attributes ) {
	global $wpdb;

	if ( empty( $attributes['selectedFolder'] ) ) {
		return '';
	}

	$where_arr   = array( '1 = 1' );
	$ids         = filebird_gallery_prepare_ids( $attributes['selectedFolder'] );
	$where_arr[] = '`folder_id` IN (' . implode( ',', $ids ) . ')';
	$in_not_in   = $wpdb->get_col( "SELECT `attachment_id` FROM {$wpdb->prefix}fbv_attachment_folder" . ' WHERE ' . implode( ' AND ', apply_filters( 'fbv_in_not_in_where_query', $where_arr, $ids ) ) );

	if ( empty( $in_not_in ) ) {
		return '';
	}

	$query = new \WP_Query(
		array(
			'post_type'      => 'attachment',
			'posts_per_page' => -1,
			'post__in'       => $in_not_in,
			'orderby'        => sanitize_text_field( $attributes['sortBy'] ),
			'order'          => sanitize_text_field( $attributes['sortType'] ),
			'post_status'    => 'inherit',
		)
	);
	$posts = $query->get_posts();
	if ( $attributes['sortBy'] == 'file_name' ) {
		if ( $attributes['sortType'] == 'ASC' ) {
			usort(
				$posts,
				function( $img1, $img2 ) {
					return ( basename( $img1->guid ) > basename( $img2->guid ) ) ? 1 : -1;
				}
			);
		} else {
			usort(
				$posts,
				function( $img1, $img2 ) {
					return ( basename( $img1->guid ) > basename( $img2->guid ) ) ? -1 : 1;
				}
			);
		}
	}
	$ulClass  = 'wp-block-filebird-block-filebird-gallery wp-block-gallery blocks-gallery-grid';
	$ulClass .= ! empty( $attributes['className'] ) ? ' ' . esc_attr( $attributes['className'] ) : '';
	$ulClass .= ' columns-' . esc_attr( $attributes['columns'] );
	$ulClass .= $attributes['isCropped'] ? ' is-cropped' : '';

	if ( count( $posts ) < 1 ) {
		return '';
	}

	$html  = '';
	$html .= '<ul class="' . esc_attr( $ulClass ) . '">';

	foreach ( $posts as $post ) {
		if ( ! wp_attachment_is_image( $post ) ) {
			continue;
		}
		$href     = '';
		$imageSrc = wp_get_attachment_image_src( $post->ID, 'full' );
		$imageSrc = $imageSrc[0];
		$imageAlt = get_post_meta( $post->ID, '_wp_attachment_image_alt', true );
		$imageAlt = empty( $imageAlt ) ? $post->post_title : $imageAlt;
		switch ( $attributes['linkTo'] ) {
			case 'media':
				$href = $imageSrc;
				break;
			case 'attachment':
				$href = get_attachment_link( $post->ID );

				break;
			default:
				break;
		}

		$img  = '<img src="' . esc_attr( $imageSrc ) . '" alt="' . esc_html( $imageAlt ) . '"';
		$img .= ' class="' . "wp-image-{$post->ID}" . '"/>';

		$li  = '<li class="blocks-gallery-item">';
		$li .= '<figure>';

		$li .= empty( $href ) ? $img : '<a href="' . esc_attr( $href ) . '">' . $img . '</a>';

		if ( $attributes['hasCaption'] ) {
			$li .= empty( $post->post_excerpt ) ? '' : '<figcaption class="blocks-gallery-item__caption">' . esc_html( $post->post_excerpt ) . '</figcaption>';
		}

		$li .= '</figure>';
		$li .= '</li>';

		$html .= $li;
	}

	$html .= '</ul>';

	wp_enqueue_style( 'filebird_gallery-block-css' );
	wp_enqueue_style('wp-block-gallery');

	return $html;
}

function filebird_gutenberg_get_images() {
	register_rest_route(
		NJFB_REST_URL,
		'gutenberg-get-images',
		array(
			'methods'             => 'POST',
			'callback'            => 'filebird_gutenberg_render_callback',
			'permission_callback' => 'filebird_gutenberg_get_images_permission_callback',
		)
	);
}

function filebird_gutenberg_get_images_permission_callback(){
	return current_user_can( 'upload_files' );
}

function filebird_gutenberg_render_callback( $request ) {
	$attributes = $request->get_params();

	$html = filebird_gallery_render( $attributes );
	wp_send_json(
		array(
			'html' => $html,
		)
	);
}

add_action( 'init', 'filebird_gallery_block_assets' );
add_action( 'rest_api_init', 'filebird_gutenberg_get_images' );
