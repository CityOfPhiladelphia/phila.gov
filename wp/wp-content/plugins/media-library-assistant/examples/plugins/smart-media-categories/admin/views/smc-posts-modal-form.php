<?php
/*
 * smc-posts-modal-form Parameters
 *
 * $form_url URL for processing form submission
 * $form_action Ajax action suffix for processing form submission
 * $sync_box HTML for Posts/All Posts Smart Media popup window
 */
?>
<form id="smc-posts-modal-form" action="<?php echo $form_url; ?>" method="post">
	<input name="action" id="smc-posts-modal-action" type="hidden" value="<?php echo $form_action; ?>">
	<div id="smc-posts-modal-div" style="display: none;">
		<input name="parent" id="smc-posts-modal-parent" type="hidden" value="">
		<input name="children[]" id="smc-posts-modal-children" type="hidden" value="">
		<?php wp_nonce_field( 'smc_find_posts', 'smc-posts-modal-ajax-nonce', false ); ?>
		<div id="smc-posts-modal-head-div">
			<?php _e( 'Select Parent', $this->plugin_slug ); ?>
			<div id="smc-posts-modal-close-div"></div>
		</div>
		<div id="smc-posts-modal-inside-div">
			<div id="smc-posts-modal-search-div">
				<label class="screen-reader-text" for="smc-posts-modal-input"><?php _e( 'Search', 'smart-media-categories' ); ?></label>
				<input name="smc_set_parent_search_text" id="smc-posts-modal-input" type="text" value="">
				<span class="spinner"></span>
				<input class="button" id="smc-posts-modal-search" type="button" value="<?php esc_attr_e( 'Search', 'smart-media-categories' ); ?>">
				&nbsp;<?php echo $post_type_dropdown; ?>
				<div class="clear"></div>
			</div>
			<div id="smc-posts-modal-titles-div">
				<div id="smc-posts-modal-current-title-div">
				<?php _e( 'For', 'smart-media-categories' ); ?>: <span id="smc-posts-modal-titles"></span>
				</div>
			</div>
			<div id="smc-posts-modal-pagination-div">
				<input class="button" id="smc-set-parent-previous" type="button" value="&laquo;">
				<input class="button" id="smc-set-parent-next" type="button" value="&raquo;">
			</div>
			<div class="clear"></div>
			<div id="smc-posts-modal-response-div">
				<input name="smc_set_parent_count" id="smc-set-parent-count" type="hidden" value="<?php echo $count; ?>">
				<input name="smc_set_parent_paged" id="smc-set-parent-paged" type="hidden" value="<?php echo $paged; ?>">
				<input name="smc_set_parent_found" id="smc-set-parent-found" type="hidden" value="<?php echo $found; ?>">
				<table class="widefat">
					<thead><tr>
						<th class="found-radio"><br /></th>
						<th><?php _e( 'Title', 'smart-media-categories' ); ?></th>
						<th class="no-break"><?php _e( 'Type', 'smart-media-categories' ); ?></th>
						<th class="no-break"><?php _e( 'Date', 'smart-media-categories' ); ?></th>
						<th class="no-break"><?php _e( 'Status', 'smart-media-categories' ); ?></th>
					</tr></thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
		<div id="smc-posts-modal-buttons-div">
				<?php submit_button( __( 'Cancel', 'smart-media-categories' ), 'button-secondary cancel alignleft', 'smc-posts-modal-cancel', false ); ?>
			<?php submit_button( __( 'Select', 'smart-media-categories' ), 'button-primary alignright', 'smc-posts-modal-submit', false ); ?>
			<div class="clear"></div>
		</div>
	</div><!-- smc-posts-modal-div -->
	<table id="found-0-table" style="display: none">
		<tbody>
			<tr id="found-0-row" class="found-posts">
				<td class="found-radio">
					<input name="found_post_id" id="found-0" type="radio" value="0">
				</td>
				<td>
					<label for="found-0">(<?php _e( 'Unattached', 'smart-media-categories' ); ?>)</label>
				</td>
				<td class="no-break">&mdash;</td>
				<td class="no-break">&mdash;</td>
				<td class="no-break">&mdash;</td>
			</tr>
		</tbody>
	</table>
	<?php echo $sync_box; ?>
</form>
