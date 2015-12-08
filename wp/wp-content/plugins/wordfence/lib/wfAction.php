<select class="wfConfigElem" id="<?php echo $throtName; ?>" name="<?php echo $throtName; ?>">
	<option value="throttle"<?php $w->sel($throtName, 'throttle'); ?>>throttle it</option>
	<option value="block"<?php $w->sel($throtName, 'block'); ?>>block it</option>
</select>
