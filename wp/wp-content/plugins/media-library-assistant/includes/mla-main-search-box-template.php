<?php
/**
 * PHP "template" for Media/Assistant submenu table Search Media box
 *
 * @package Media Library Assistant
 * @since 1.90
 */

/**
 * Harmless declaration to suppress phpDocumentor "No page-level DocBlock" error
 *
 * @global $post
 */
global $post;

if ( !empty( $_REQUEST['s'] ) ) {
	$search_value = esc_attr( stripslashes( trim( $_REQUEST['s'] ) ) );
	$search_fields = isset ( $_REQUEST['mla_search_fields'] ) ? $_REQUEST['mla_search_fields'] : array();
	$search_connector = $_REQUEST['mla_search_connector'];
} else {
	$search_value = MLACore::mla_get_option( MLACoreOptions::MLA_SEARCH_MEDIA_FILTER_DEFAULTS );
	$search_fields = $search_value['search_fields'];
	$search_connector = $search_value['search_connector'];
	$search_value = '';
}

if ( 'checked' == MLACore::mla_get_option( MLACoreOptions::MLA_SEARCH_MEDIA_FILTER_SHOW_CONTROLS ) ) {
	$controls_style = 'style="display: inline;"';
} else {
	$controls_style = 'style="display: none;"';
}

$supported_taxonomies = MLACore::mla_supported_taxonomies('support');
if ( empty( $supported_taxonomies ) ) {
	$terms_style = 'style="display: none;"';
	unset( $search_fields['terms'] );
} else {
	$terms_style = 'style="display: inline;"';
}
?>
<p class="search-box">
<label class="screen-reader-text" for="mla-media-search-input"><?php _e( 'Search Media', 'media-library-assistant' ); ?></label>
<input name="s" id="mla-media-search-input" type="text" size="45" value="<?php echo $search_value ?>" />
<input name="mla-search-submit" class="button" id="search-submit" type="submit" value="<?php _e( 'Search Media', 'media-library-assistant' ); ?>" /><br />
<span <?php echo $controls_style ?>>
<input name="mla_search_connector" type="radio" <?php echo ( 'OR' === $search_connector ) ? '' : 'checked="checked"'; ?> value="AND" /><?php _e( 'and', 'media-library-assistant' ); ?>

<input name="mla_search_connector" type="radio" <?php echo ( 'OR' === $search_connector ) ? 'checked="checked"' : ''; ?> value="OR" /><?php _e( 'or', 'media-library-assistant' ); ?>

<input name="mla_search_fields[]" id="search-title" type="checkbox" <?php echo ( in_array( 'title', $search_fields ) ) ? 'checked="checked"' : ''; ?> value="title" /><?php _e( 'Title', 'media-library-assistant' )?>&nbsp;
<input name="mla_search_fields[]" id="search-name" type="checkbox" <?php echo ( in_array( 'name', $search_fields ) ) ? 'checked="checked"' : ''; ?> value="name" /><?php _e( 'Name', 'media-library-assistant' )?>&nbsp;
<input name="mla_search_fields[]" id="search-alt-text" type="checkbox" <?php echo ( in_array( 'alt-text', $search_fields ) ) ? 'checked="checked"' : ''; ?> value="alt-text" /><?php _e( 'ALT Text', 'media-library-assistant' )?>&nbsp;
<input name="mla_search_fields[]" id="search-excerpt" type="checkbox" <?php echo ( in_array( 'excerpt', $search_fields ) ) ? 'checked="checked"' : ''; ?> value="excerpt" /><?php _e( 'Caption', 'media-library-assistant' )?>&nbsp;
<input name="mla_search_fields[]" id="search-content" type="checkbox" <?php echo ( in_array( 'content', $search_fields ) ) ? 'checked="checked"' : ''; ?> value="content" /><?php _e( 'Description', 'media-library-assistant' )?>&nbsp;
<span <?php echo $terms_style ?>><input name="mla_search_fields[]" id="terms-search" type="checkbox" <?php echo ( in_array( 'terms', $search_fields ) ) ? 'checked="checked"' : ''; ?> value="terms" /><?php _e( 'Terms', 'media-library-assistant' )?></span>
</span>
</p>
