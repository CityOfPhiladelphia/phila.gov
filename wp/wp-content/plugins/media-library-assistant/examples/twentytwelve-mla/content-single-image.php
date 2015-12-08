<?php
/**
 * The template used for displaying "attachment page" content in page-single-image.php
 *
 * You must select the attachment you want by adding a query parameter
 * to the URL, e.g., "?post_id=5" where 5 is the ID of the attachment.
 *
 * The global $post variable contains the post object of the calling page.
 *
 * @package Media Library Assistant
 * @subpackage MLA_Child_Theme
 * @version 1.00
 * @since MLA 1.80
 */

/**
 * The original static page that brought us here
 *
 * @global $post
 */
global $post;
?>
<?php
/*
 * Emulate the logic in edit_post_link(), because we want to edit the static page, not the attachment
 */
$before = '<span class="edit-link">';
$url = get_edit_post_link( $post->ID );
$link = $link = '<a class="post-edit-link" href="' . $url . '">' . __( 'Edit', 'mla-child-theme' ) . '</a>';
$after = '</span>';
$edit_post_link = $before . apply_filters( 'edit_post_link', $link, $post->ID ) . $after;

/*
 * The attachment's ID must be set as an HTML query parameter
 */
$post_id = isset( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : 0;
if ( $post_id ) {
	$query = new WP_Query( array( 'p' => $post_id, 'post_type' => 'attachment', 'post_status' => 'inherit', 'orderby' => 'none', 'update_post_term_cache' => false ) );
} else {
	echo '<h1>ERROR: No Post ID</h1>';
	the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentytwelve' ) );
	return;
}

if ( $query->have_posts() ) {
	$page = $post; // in case we need it later
	$query->the_post(); // simulate "the loop"
} else {
	echo '<h1>ERROR: No Attachment Object</h1>';
	the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentytwelve' ) );
	return;
}
?>
	<article id="post-<?php the_ID(); ?>" class="post-<?php the_ID(); ?> attachment type-attachment status-inherit hentry">
		<header class="entry-header">
			<h1 class="entry-title"><?php the_title(); ?></h1>
				<footer class="entry-meta">
					<?php
						$metadata = wp_get_attachment_metadata();
						printf( __( '<span class="meta-prep meta-prep-entry-date">Published </span> <span class="entry-date"><time class="entry-date" datetime="%1$s">%2$s</time></span> at <a href="%3$s" title="Link to full-size image">%4$s &times; %5$s</a> .', 'twentytwelve' ),
							esc_attr( get_the_date( 'c' ) ),
							esc_html( get_the_date() ),
							esc_url( wp_get_attachment_url() ),
							$metadata['width'],
							$metadata['height']
						);
					?>
					<?php echo $edit_post_link; ?>
				</footer><!-- .entry-meta -->
			</header><!-- .entry-header -->

			<div class="entry-content">
				<div class="entry-attachment">
					<div class="attachment">
						<a href="<?php echo esc_url( wp_get_attachment_url() ); ?>" title="<?php the_title_attribute(); ?>" rel="attachment"><?php
						/**
						 * Filter the image attachment size to use.
						 *
						 * @since Twenty Twelve 1.0
						 *
						 * @param array $size {
						 *     @type int The attachment height in pixels.
						 *     @type int The attachment width in pixels.
						 * }
						 */
						$attachment_size = apply_filters( 'twentytwelve_attachment_size', array( 960, 960 ) );
						echo wp_get_attachment_image( $post->ID, $attachment_size );
						?></a>

						<?php if ( ! empty( $post->post_excerpt ) ) : ?>
						<div class="entry-caption">
							<?php the_excerpt(); ?>
						</div>
						<?php endif; ?>
					</div><!-- .attachment -->
				</div><!-- .entry-attachment -->
				<div class="entry-description">
					<?php echo esc_attr( $post->post_content );	?><br />
					&nbsp;
				</div><!-- .entry-description -->
			</div><!-- .entry-content -->

			<div class="entry-terms">
			<?php
			$attr = array();
			/*
			 * You can change the default taxonomy slug by adding a query variable, e.g.,
			 * "?taxonomy=attachment_category"
			 */
			if ( ! empty( $_REQUEST['taxonomy'] ) ) {
				$attr['taxonomy'] = $_REQUEST['taxonomy'];
			}

			/*
			 * You can change the default destination page by adding a query variable, e.g., 
			 * "?page_path=some-other-page"
			 */
			if ( ! empty( $_REQUEST['page_path'] ) ) {
				$attr['page_path'] = $_REQUEST['page_path'];
			}

			mla_custom_terms_list( get_the_ID(), $attr ); // Child theme function
			?>
			</div><!-- .entry-terms -->

		<footer class="entry-meta">
			<?php echo $edit_post_link; ?>
		</footer><!-- .entry-meta -->
	</article><!-- #post -->
