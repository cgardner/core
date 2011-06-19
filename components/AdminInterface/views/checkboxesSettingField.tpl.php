<?php
$x = 0;
foreach($setting['values'] as $option) {
	$label = ((count($setting['labels']) > $x) ? $setting['labels'][$x] : 'None');
	if(isset($setting['selected'])) {
		$selected = in_array($option, $setting['selected']);
	} else {
		$selected = false;
	}
	?>
	<div class="formItem checkbox">
	<?php 
		echo $this->fh->checkboxTag($setting['name']."[]", $option, $selected);
		echo $this->fh->labelFor($label, $setting['name'].'-'.$option, array('class' => 'checkbox'));
	?>
	</div>
	<?php $x++; } 
?>