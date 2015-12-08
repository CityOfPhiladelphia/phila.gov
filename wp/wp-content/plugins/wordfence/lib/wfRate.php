<select class="wfConfigElem" id="<?php echo $rateName; ?>" name="<?php echo $rateName; ?>">
	<option value="DISABLED"<?php $w->sel($rateName, 'DISABLED'); ?>>Unlimited</option>
	<option value="1"<?php $w->sel($rateName, '1'); ?>>1 per minute (1 every 60 seconds)</option>
	<option value="2"<?php $w->sel($rateName, '2'); ?>>2 per minute (1 every 30 seconds)</option>
	<option value="3"<?php $w->sel($rateName, '3'); ?>>3 per minute (1 every 20 seconds)</option>
	<option value="4"<?php $w->sel($rateName, '4'); ?>>4 per minute (1 every 15 seconds)</option>
	<option value="5"<?php $w->sel($rateName, '5'); ?>>5 per minute (1 every 12 seconds)</option>
	<option value="10"<?php $w->sel($rateName, '10'); ?>>10 per minute (1 every 6 seconds)</option>
	<option value="15"<?php $w->sel($rateName, '15'); ?>>15 per minute (1 every 4 seconds)</option>
	<option value="30"<?php $w->sel($rateName, '30'); ?>>30 per minute (1 every 2 seconds)</option>
	<option value="60"<?php $w->sel($rateName, '60'); ?>>60 per minute (1 per second)</option>
	<option value="120"<?php $w->sel($rateName, '120'); ?>>120 per minute (2 per second)</option>
	<option value="240"<?php $w->sel($rateName, '240'); ?>>240 per minute (4 per second)</option>
	<option value="480"<?php $w->sel($rateName, '480'); ?>>480 per minute (8 per second)</option>
	<option value="960"<?php $w->sel($rateName, '960'); ?>>960 per minute (16 per second)</option>
	<option value="1920"<?php $w->sel($rateName, '1920'); ?>>1920 per minute (32 per second)</option>
</select>

