<?php
/**
 * The template used for displaying "Tosca30 Dropdown" content in page-tosca30-dropdown.php
 *
 * Replaces the wp_list_categories() item count with an accurate, padded count of the
 * attachments assigned to each term.
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
<?php
/**
 * Class MNA_Pad_Counts_Walker adds accurate, padded attachment counts to taxonomy terms.
 *
 * Class Walker is defined in /wp-includes/class-wp-walker.php
 * Class Walker_Category is defined in /wp-includes/category-template.php
 */
class MNA_Pad_Counts_Walker extends Walker_Category {
	/**
	 * MLA Terms
	 *
	 * @var array
	 */
	private $mla_terms = array();

	/**
	 * Constructor - set the MLA Terms.
	 *
	 * @param string Taxonomy name/slug.
	 */
	function __construct( $taxonomy ) {
		$attr = array (
			'taxonomy' => $taxonomy,
			'pad_counts' => 'true',
		);
		$terms = MLAShortcodes::mla_get_terms( $attr );
		unset( $terms['found_rows'] );

		foreach ( $terms as $term ) {
			$this->mla_terms[ $term->term_taxonomy_id ] = $term->count;
		}
	}

	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 *
	 * @param string Passed by reference. Used to append additional content.
	 * @param object Taxonomy data object.
	 * @param int    Depth of category in reference to parents. Default 0.
	 * @param array  An array of arguments. @see wp_list_categories()
	 * @param int    ID of the current category.
	 */
	function start_el( &$output, $taxonomy_object, $depth = 0, $args = array(), $id = 0 ) {

		if ( isset( $this->mla_terms[ $taxonomy_object->term_taxonomy_id ] ) ) {
			$taxonomy_object->count = $this->mla_terms[ $taxonomy_object->term_taxonomy_id ];
		}

		parent::start_el( $output, $taxonomy_object, $depth, $args, $id );
	}
}// Class MNA_Pad_Counts_Walker

$taxonomies = get_object_taxonomies( 'attachment', 'objects' );

foreach ( $taxonomies as $taxonomy ) {
	echo '<h3>' . $taxonomy->labels->name . '</h3>';

	unset( $checklist_walker );
	$checklist_walker = new MNA_Pad_Counts_Walker( $taxonomy->name );
	$args = array(
		'taxonomy'		=> $taxonomy->name,
		'hierarchical'	=> 1,
		'hide_empty'	=> 0,
		'pad_counts'	=> 1,
		'show_count'	=> 1,
		'title_li'		=> '',
		'walker'		=> $checklist_walker,
	);

	echo '<h4>wp_list_categories</h4>';
	wp_list_categories( $args );

	echo '<h4>mla_tag_cloud</h4>';
	echo '<table><tr><td>pad_counts=false<br>';
	echo do_shortcode( "[mla_tag_cloud taxonomy={$taxonomy->name} mla_output=list pad_counts=false]" );
	echo '</td><td>pad_counts=true<br>';
	echo do_shortcode( "[mla_tag_cloud taxonomy={$taxonomy->name} mla_output=list pad_counts=true]" );
	echo '</td></tr></table>';
}

?>
	</div><!-- .entry-content -->
	<footer class="entry-meta">
		<?php edit_post_link( __( 'Edit', 'twentytwelve' ), '<span class="edit-link">', '</span>' ); ?>
	</footer>
	<!-- .entry-meta --> 
</article>
<!-- #post --> 
