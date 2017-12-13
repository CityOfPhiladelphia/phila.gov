<?php
/*
 * Taxonomy Options Parameters
 *
 * $tax_attr taxonomy slug, attribute format
 */
?>
<div class="smc-sync-taxonomy-options" id="smc-sync-<?php echo $tax_attr; ?>-options">
<input type="radio" name="tax_action[<?php echo $tax_attr; ?>]" id="smc-sync-<?php echo $tax_attr; ?>-sync" checked="checked" value="sync" /> <?php _e( 'Sync', $this->plugin_slug ); ?>&nbsp;
<input type="radio" name="tax_action[<?php echo $tax_attr; ?>]" id="smc-sync-<?php echo $tax_attr; ?>-ignore" value="ignore" /> <?php _e( 'No Change', 'smart-media-categories' ); ?>
</div>
