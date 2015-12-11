<?php
/**
 * Backbone/JavaScript template for Media Library Assistant Media Manager enhancements
 *
 * @package Media Library Assistant
 * @since 1.80
 */

/**
 * Harmless declaration to suppress phpDocumentor "No page-level DocBlock" error
 *
 * @global $post
 */
global $post;

$supported_taxonomies = MLACore::mla_supported_taxonomies('support');
if ( empty( $supported_taxonomies ) ) {
	$terms_style = 'style="display: none;"';
} else {
	$terms_style = 'style="display: inline;"';
}
?>
<script type="text/html" id="tmpl-mla-search-box">
    <div style="display: inline-block">
		<label class="screen-reader-text" for="mla-media-search-input"><?php _e( 'Search Media', 'media-library-assistant' ); ?>:</label>
	    <input name="s[mla_search_value]" class="search" id="mla-media-search-input" style="width: 100%; max-width: 100%" type="search" value="{{ data.searchValue }}" placeholder="{{ data.searchBoxPlaceholder }}" />
	</div>
	<input name="mla_search_submit" class="button media-button mla-search-submit-button" id="mla-search-submit" type="submit" style="float: none" value="<?php _e( 'Search', 'media-library-assistant' ); ?>"  /><br>
    <ul class="mla-search-options" style="{{ data.searchBoxControlsStyle }}">
        <li>
            <input type="radio" name="s[mla_search_connector]" value="AND" <# if ( 'OR' !== data.searchConnector ) { #>checked="checked"<# } #> />
            <?php _e( 'and', 'media-library-assistant' ); ?>
        </li>
        <li>
            <input type="radio" name="s[mla_search_connector]" value="OR" <# if ( 'OR' === data.searchConnector ) { #>checked="checked"<# } #> />
            <?php _e( 'or', 'media-library-assistant' ); ?>
        </li>
        <li>
            <input type="checkbox" name="s[mla_search_title]" id="search-title" value="title" <# if ( -1 != data.searchFields.indexOf( 'title' ) ) { #>checked<# } #> />
            <?php _e( 'Title', 'media-library-assistant' ); ?>
        </li>
        <li>
            <input type="checkbox" name="s[mla_search_name]" id="search-name" value="name" <# if ( -1 != data.searchFields.indexOf( 'name' ) ) { #>checked<# } #> />
            <?php _e( 'Name', 'media-library-assistant' ); ?>
        </li>
        <li>
            <input type="checkbox" name="s[mla_search_alt_text]" id="search-alt-text" value="alt-text" <# if ( -1 != data.searchFields.indexOf( 'alt-text' ) ) { #>checked<# } #> />
            <?php _e( 'ALT Text', 'media-library-assistant' ); ?>
        </li>
		<br>
        <li>
            <input type="checkbox" name="s[mla_search_excerpt]" id="search-excerpt" value="excerpt" <# if ( -1 != data.searchFields.indexOf( 'excerpt' ) ) { #>checked<# } #> />
            <?php _e( 'Caption', 'media-library-assistant' ); ?>
        </li>
        <li>
            <input type="checkbox" name="s[mla_search_content]" id="search-content" value="content" <# if ( -1 != data.searchFields.indexOf( 'content' ) ) { #>checked<# } #> />
            <?php _e( 'Description', 'media-library-assistant' ); ?>
        </li>
		<span <?php echo $terms_style ?>>
        <li>
            <input type="checkbox" name="s[mla_search_terms]" id="search-terms" value="terms" <# if ( -1 != data.searchFields.indexOf( 'terms' ) ) { #>checked<# } #> />
            <?php _e( 'Terms', 'media-library-assistant' ); ?>
        </li>
		</span>
    </ul>
</script>
<script type="text/html" id="tmpl-mla-terms-search-button">
	<input type="button" name="mla_terms_search" id="mla-terms-search" class="button media-button button-large mla-terms-search-button" value="<?php _e( 'Terms Search', 'media-library-assistant' ); ?>"  />
</script>
<script type="text/html" id="tmpl-mla-simulate-search-button">
	<input style="display:none" type="button" name="mla_search_submit" id="mla-search-submit" class="button" value="<?php _e( 'Search', 'media-library-assistant' ); ?>"  />
</script>