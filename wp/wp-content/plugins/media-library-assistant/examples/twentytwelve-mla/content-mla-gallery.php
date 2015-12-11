<?php
/**
 * The template for displaying posts in the Image post format,
 * called from the taxonomy.php template.
 *
 * @package Media Library Assistant
 * @subpackage MLA_Child_Theme
 * @version 1.00
 * @since MLA 1.91
 */

/**
 * Harmless declaration to suppress phpDocumentor "No page-level DocBlock" error
 *
 * @global $wp_query
 */
global $wp_query;
?>

			<header class="archive-header">
				<h1 class="archive-title">
					MLA Gallery for <?php echo $wp_query->query_vars['term']; ?> in <?php echo $wp_query->query_vars['taxonomy']; ?>
					<?php echo do_shortcode( sprintf( '[mla_gallery %1$s="%2$s" mla_caption="{+title+}" mla_debug=false]', $wp_query->query_vars['taxonomy'], $wp_query->query_vars['term'] ) ); ?>
				</h1>
			</header>
