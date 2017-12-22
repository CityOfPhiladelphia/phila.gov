<?php
/*
 * Tag Block Parameters
 *
 * $tax_name taxonomy name, display format
 * $tax_attr taxonomy slug, attribute format
 * $taxonomy_options sync/ignore radio buttons for one taxonomy
 */
?>
<div id="smc-sync-<?php echo $tax_attr; ?>-block">
	<label class="smc-sync-tags">
		<span class="title"><?php echo $tax_name; ?></span>
		<textarea name="tax_input[<?php echo $tax_attr; ?>]" class="smc-tags" id="smc-tax-input-<?php echo $tax_attr; ?>" cols="22" rows="1"></textarea>
	</label>
	<?php echo $taxonomy_options; ?>
</div>
