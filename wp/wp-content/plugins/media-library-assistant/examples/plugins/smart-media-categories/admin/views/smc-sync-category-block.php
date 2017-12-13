<?php
/*
 * Category Block Parameters
 *
 * $tax_name taxonomy name, display format
 * $tax_attr taxonomy slug, attribute format
 * $tax_checklist checklist for one taxonomy
 * $taxonomy_options sync/ignore radio buttons for one taxonomy
 */
?>
<div id="smc-sync-<?php echo $tax_attr; ?>-block">
	<span class="title smc-sync-categories-label"><?php echo $tax_name; ?>
		<span class="catshow"><?php _e( 'more', $this->plugin_slug ); ?></span>
		<span class="cathide" style="display:none;"><?php _e( 'less', 'smart-media-categories' ); ?></span>
	</span>
	<input id="smc-tax-input-<?php echo $tax_attr; ?>" type="hidden" name="tax_input[<?php echo $tax_attr; ?>][]" value="0" />
	<ul class="cat-checklist" id="smc-tax-checklist-<?php echo $tax_attr; ?>">
	<?php echo $tax_checklist; ?>
	</ul>
	<?php echo $taxonomy_options; ?>
</div>
