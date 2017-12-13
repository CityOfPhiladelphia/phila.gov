<?php
/**
 * The template used for displaying "JKEast Dropdown" content in page-jkeast-dropdown.php
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
		<h1 class="entry-title">
			<?php the_title(); ?>
		</h1>
	</header>
	<div class="entry-content">
		<?php the_content(); ?>
<script>
( function( $ ) {
	$(document).ready(function () {
		var selected = $('#gallery').val();
		$('#'+ selected).show();
		$('#gallery').change(function () {
			var $select = $( this ),
				selected = $select.val();
				if ( selected === "option0"){
					$('div.group').hide();
					$('#option0').show();
				} else {
					$('div.group').hide();
					$('#'+ selected).show();
				}
		})
	});
})( jQuery );
</script>
		<?php 
/**
 * Custom Taxonomy Dropdown Control
 *
 * @since 1.00
 *
 * @param	string	Taxonomy slug
 * @param	string	Order by field
 * @param	string	Sort order
 * @param	string	Not used
 * @param	string	HTML name= value
 * @param	mixed	NULL/"Select All" label
 * @param	mixed	NULL/"Select None" label
 * @param	integer	Index of the inital selection
 *
 * @return	void	Echoes HTML for the dropdown control
 */
function custom_taxonomy_dropdown( $taxonomy, $orderby = 'date', $order = 'DESC', $limit = '-1', $name, $show_option_all = null, $show_option_none = null, $selected=0 ) {
	$args = array(
		'orderby' => $orderby,
		'order' => $order,
		'exclude' => '120'
	);
	$terms = get_terms( $taxonomy, $args );
	$name = ( $name ) ? $name : $taxonomy;
	$y=0;
	if ( $terms ) {
		printf( '<select name="%s" id="gallery">', esc_attr( $name ) );
		if ( $show_option_all ) {
			printf( '<option class="group" value="option0">%s</option>', esc_html( $show_option_all ) );
		}
		if ( $show_option_none ) {
			printf( '<option value="-1">%s</option>', esc_html( $show_option_none ) );
		}
		foreach ( $terms as $term ) {
			$y++;
			if ( $selected == $y ) {
			printf( '<option name="%s" class="group" value="option'.$y.'" selected>%s</option>', esc_attr( $term->slug ), esc_html( $term->name ) );
			} else {
			printf( '<option name="%s" class="group" value="option'.$y.'" >%s</option>', esc_attr( $term->slug ), esc_html( $term->name ) );
			}
		}
		print( '</select>' );
	}
}

// Find the most recent gallery pagination parameter,
// which determines the current dropdown selection.
$page_tag = 'mla_current_';
$current_uri = $_SERVER['REQUEST_URI'];
$pos = strrpos( $current_uri, $page_tag );
if ( $pos ) {
	$base_uri = substr( $current_uri, 0, strpos( $current_uri, '?' ) );
	$selected = substr( $current_uri, $pos + strlen( $page_tag ) );
	// Keep only the most recent pagination value
	$_SERVER['REQUEST_URI'] = $base_uri . '?' . $page_tag . $selected;
	$selected = absint( substr( $current_uri, $pos + strlen( $page_tag ) ) );
} else {
	$selected = 0;
}

custom_taxonomy_dropdown( 'attachment_category', 'date', 'ASC', '5', 'attachment_category', 'Select All', NULL, $selected );
?>
		<?php 
$terms = get_terms("attachment_category", "exclude=120");

if ( !empty( $terms ) && !is_wp_error( $terms ) ){
	$xyz=0;
	foreach ( $terms as $term ) {
		$n = $term->slug;
		$page_parameter = $page_tag . ++$xyz;
		?>
		<div id="option<?php echo $xyz; ?>" class="group" style="display: none"> <?php echo do_shortcode('[mla_gallery mla_alt_shortcode=gallery orderby="title" link="file" order="DESC" post_parent=all posts_per_page=3 attachment_category="'.$n.'" mla_page_parameter=' . $page_parameter . ']'); ?>
			<div class="pgn-btns<?php echo $xyz; ?>">
				<div style="clear: both; float: left"> <?php echo do_shortcode('[mla_gallery mla_output="previous_page,first" orderby="title" order="DESC" post_parent=all posts_per_page=3 attachment_category="'.$n.'" mla_page_parameter=' . $page_parameter . ']')?></div>
				<div style="float: right"> <?php echo do_shortcode('[mla_gallery mla_output="next_page,last" orderby="title" order="DESC" post_parent=all posts_per_page=3 attachment_category="'.$n.'" mla_page_parameter=' . $page_parameter . ']')?></div>
			</div>
		</div>
		<?php
     }
 }
 ?>
		<div id="option0" class="group" style="display: none"><?php echo do_shortcode('[mla_gallery mla_alt_shortcode=gallery orderby="title" order="DESC" link="file" post_parent=all posts_per_page=6 mla_page_parameter=' . $page_tag . '0]'); ?>
			<div class="pgn-btns">
				<div style="clear: both; float: left"> <?php echo do_shortcode('[mla_gallery mla_output="previous_page,first" orderby="title" order="DESC" post_parent=all posts_per_page=6 mla_page_parameter=' . $page_tag . '0]')?></div>
				<div style="float: right"> <?php echo do_shortcode('[mla_gallery mla_output="next_page,last" orderby="title" order="DESC" post_parent=all posts_per_page=6 mla_page_parameter=' . $page_tag . '0]')?></div>
			</div>
		</div>
		<div style="clear: both;">&nbsp;</div>
	</div>
	</div><!-- .entry-content -->
	<footer class="entry-meta">
		<?php edit_post_link( __( 'Edit', 'twentytwelve' ), '<span class="edit-link">', '</span>' ); ?>
	</footer>
	<!-- .entry-meta --> 
</article>
<!-- #post --> 
