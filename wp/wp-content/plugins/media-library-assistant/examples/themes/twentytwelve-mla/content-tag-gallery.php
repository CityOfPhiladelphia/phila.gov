<?php
/**
 * The template used for displaying "Tag Gallery" content in page-tag-gallery.php
 *
 * The default taxonomy slug is "attachment_tag". You can select the taxonomy you want by adding
 * a query parameter to the URL, e.g., "?my_taxonomy=attachment_category".
 *
 * The default taxonomy term is empty. You must select the term you want by adding
 * a query parameter to the URL, e.g., "?my_term=yellow".
 *
 * @package Media Library Assistant
 * @subpackage MLA_Child_Theme
 * @version 1.00
 * @since MLA 1.80
 */
 
/**
 * Harmless declaration to suppress phpDocumentor "No page-level DocBlock" error
 *
 * @global $post
 */
global $post;
?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header">
			<?php the_post_thumbnail(); ?>
			<h1 class="entry-title"><?php the_title(); ?></h1>
		</header>

		<div class="entry-content">
			<?php the_content(); ?>
			<?php
			$attr = array(
				'page' => '/single-image/',
				'taxonomy' => 'attachment_tag',
				'term' => '',
				'post_mime_type' => 'image',
				'posts_per_page' =>1,
				'current_page' => 1
			);

			/*
			 * You can change the defaults by adding query variables, e.g.,
			 * "?my_taxonomy=attachment_category"
			 * "?my_term=term-slug"
			 */
			if ( ! empty( $_REQUEST['my_taxonomy'] ) ) {
				$attr['taxonomy'] = $_REQUEST['my_taxonomy'];
			}

			if ( ! empty( $_REQUEST['my_term'] ) ) {
				$attr['term'] = $_REQUEST['my_term'];
			}

			if ( ! empty( $_REQUEST['post_mime_type'] ) ) {
				$attr['post_mime_type'] = $_REQUEST['post_mime_type'];
			}

			if ( ! empty( $_REQUEST['posts_per_page'] ) ) {
				$attr['posts_per_page'] = $_REQUEST['posts_per_page'];
			}

			if ( ! empty( $_REQUEST['mla_paginate_current'] ) ) {
				$attr['current_page'] = $_REQUEST['mla_paginate_current'];
			}

			$count = mla_paginated_term_gallery( $attr ); // Child theme function
			?>
		<div class="pagination loop-pagination">
			<?php echo do_shortcode( sprintf( '[mla_gallery mla_paginate_rows="%1$s" mla_output="previous_page,first" numberposts="%2$s" mla_link_class="prev page-numbers" mla_link_text="{Page {+current_page+} of {+last_page+}}"]', $count, $attr['posts_per_page'] ) ); ?>
			<?php echo do_shortcode( sprintf( '[mla_gallery mla_paginate_rows="%1$s" mla_output="paginate_links,prev_next" numberposts="%2$s" mla_link_class="page-numbers" mla_prev_text="&#9668;" mla_next_text="&#9658;"]', $count, $attr['posts_per_page'] ) ); ?>
		</div>
		<!-- This alternative is basic Previous/Next pagination
		<div class="pagination loop-pagination">
			<?php echo do_shortcode( sprintf( '[mla_gallery mla_paginate_rows="%1$s" mla_output="previous_page" numberposts="%2$s" mla_link_class="prev page-numbers" mla_link_text="<span class=\'meta-nav\'>&#9668;</span> Previous"]', $count, $attr['posts_per_page'] ) ); ?>
			<?php echo do_shortcode( sprintf( '[mla_gallery mla_paginate_rows="%1$s" mla_output="next_page" numberposts="%2$s" mla_link_class="next page-numbers" mla_link_text="Next <span class=\'meta-nav\'>&#9658;</span>"]', $count, $attr['posts_per_page'] ) ); ?>
		</div>
		This alternative is basic Previous/Next pagination -->
			<?php //wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'twentytwelve' ), 'after' => '</div>' ) ); ?>
		</div><!-- .entry-content -->
		<footer class="entry-meta">
			<?php edit_post_link( __( 'Edit', 'twentytwelve' ), '<span class="edit-link">', '</span>' ); ?>
		</footer><!-- .entry-meta -->
	</article><!-- #post -->
