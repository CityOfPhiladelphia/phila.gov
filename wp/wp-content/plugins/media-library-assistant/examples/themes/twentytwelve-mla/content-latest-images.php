<?php
/**
 * The template used for displaying the "Latest Images" content in page-latest-images.php
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
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header">
			<?php the_post_thumbnail(); ?>
			<h1 class="entry-title"><?php the_title(); ?></h1>
		</header>

		<div class="entry-content">
			<?php the_content(); ?>
			<?php
			/*
			 * Adjust these parameters if you want more than one page, e.g.,
			 * $images_per_page = 25;
			 * $maximum_images = 100;
			 * will produce four pages of twenty five images each. Make sure you have
			 * more images than whatever you code in $maximum_images
			 */
			 $images_per_page = 25;
			 $maximum_images = 25;
			 
			/*
			 * Display the most recent images
			 * An alternate orderby="ID DESC" may offer better performance
			 */
			echo do_shortcode( sprintf( '[mla_gallery orderby="date DESC" numberposts=%1$s post_parent=all update_post_term_cache=false]', $images_per_page ) );
			?>

			<?php if ( $maximum_images > $images_per_page ) : ?>
			<div class="pagination loop-pagination">
				<?php echo do_shortcode( sprintf( '[mla_gallery mla_paginate_rows="%1$s" mla_output="previous_page,first" numberposts="%2$s" mla_link_class="prev page-numbers" mla_link_text="{Page {+current_page+} of {+last_page+}}"]', $maximum_images, $images_per_page ) ); ?>
				<?php echo do_shortcode( sprintf( '[mla_gallery mla_paginate_rows="%1$s" mla_output="paginate_links,prev_next" numberposts="%2$s" mla_link_class="page-numbers" mla_prev_text="&#9668;" mla_next_text="&#9658;"]', $maximum_images, $images_per_page ) ); ?>
			</div>

			<!-- This alternative is basic Previous/Next pagination
			<div class="pagination loop-pagination">
				<?php echo do_shortcode( sprintf( '[mla_gallery mla_paginate_rows="%1$s" mla_output="previous_page" numberposts="%2$s" mla_link_class="prev page-numbers" mla_link_text="<span class=\'meta-nav\'>&#9668;</span> Previous"]', $maximum_images, $images_per_page ) ); ?>
				<?php echo do_shortcode( sprintf( '[mla_gallery mla_paginate_rows="%1$s" mla_output="next_page" numberposts="%2$s" mla_link_class="next page-numbers" mla_link_text="Next <span class=\'meta-nav\'>&#9658;</span>"]', $maximum_images, $images_per_page ) ); ?>
			</div>
			This alternative is basic Previous/Next pagination -->
			<?php endif; ?>

			<?php //wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'twentytwelve' ), 'after' => '</div>' ) ); ?>
		</div><!-- .entry-content -->
		<footer class="entry-meta">
			<?php edit_post_link( __( 'Edit', 'twentytwelve' ), '<span class="edit-link">', '</span>' ); ?>
		</footer><!-- .entry-meta -->
	</article><!-- #post -->
