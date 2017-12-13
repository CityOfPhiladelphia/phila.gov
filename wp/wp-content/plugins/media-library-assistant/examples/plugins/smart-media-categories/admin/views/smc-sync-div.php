<?php
/*
 * smc-sync-div Parameters
 *
 * $middle_column HTML for hierarchical taxonomies
 * $right_column HTML for flat taxonomies
 */
?>
	<div class="smc-sync-box" id="smc-sync-div" style="display: none;">
		<div class="smc-sync-box-head" id="smc-sync-head">
			<?php _e( 'Smart Media Categories', $this->plugin_slug ); ?>
			<div id="smc-sync-close"></div>
		</div>
		<div class="smc-sync-box-inside" id="smc-sync-inside">
			<fieldset class="smc-sync-col-left">
				<div class="smc-sync-col" id="smc-sync-col-left">
					<span class="title"><?php _e( 'Sync List', 'smart-media-categories' ); ?></span>
					<div id="smc-sync-children-div"></div>
				</div>
			</fieldset>
			<?php echo $middle_column; ?>
			<?php echo $right_column; ?>
			<fieldset class="smc-sync-col-right">
				<div class="smc-sync-col" id="smc-sync-parent-blocks">
					<div id="smc-sync-parent-block">
						<span class="title"><?php _e( 'Parent', 'smart-media-categories' ); ?></span>
						<span class="smc-parent" id="smc-current-parent" ></span>
						<?php submit_button( __( 'Change', 'smart-media-categories' ), 'button-primary alignright', 'smc-sync-reattach', false ); ?>
					</div>
				</div>
			</fieldset>
			<div class="smc-sync-box-buttons" id="smc-sync-buttons-div">
				<?php submit_button( __( 'Cancel', 'smart-media-categories' ), 'button-secondary cancel alignleft', 'smc-sync-cancel', false ); ?>
				<?php submit_button( __( 'Update', 'smart-media-categories' ), 'button-primary alignright', 'smc-sync-update', false ); ?>
	            <span id="smc-sync-error" style="display:none"></span>
				<div class="clear"></div>
			</div>
		</div>
	</div><!-- smc-sync-div -->
