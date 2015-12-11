<tr>
	<th style="text-align: right;"><?php echo $dayName; ?></th>
	<td>Morning:</td>
	<?php
	for($i = 0; $i <= 23; $i++){ 
		$cell = '<td>';
		if($i > 11){
			$hour = $i - 12;
			if($hour == 0){ $hour = 12; }
			
			$cell .= '<span style="color: #3366CC;">' . sprintf('%02d', $hour) . '</span>';
		} else {
			$cell .= '<span style="color: #CC6633;">' . sprintf('%02d', $i) . '</span>';
		}
		if($i == 11){
			$cell .= '&nbsp;&nbsp;Afternoon:';
		}
		$cell .= '</td>'; 
		echo $cell;
	}
	echo '</tr><tr><th></th><td></td>';
	for($hour = 0; $hour <= 23; $hour++){ 
		$checked = ( isset( $sched[$dayIndex] ) && $sched[$dayIndex][$hour] ? 'checked' : '');
		echo '<td><input class="wfSchedCheckbox" type="checkbox" id="wfSchedDay_' . $dayIndex . '_' . $hour . '" ' . $checked . ' /></td>'; 
	}
	?>
	</td>
</tr>
<tr><td colspan="27">&nbsp;</td></tr>
