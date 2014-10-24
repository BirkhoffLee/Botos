<?php
if($arguments[1] == 'disable'){
	$this->disableBlock = true;
	$this->say('[System] Disabled blocking messages.', 'Error');
	$this->Submitlog('[System] Disabled blocking messages.');
} elseif($arguments[1] == 'enable'){
	$this->disableBlock = false;
	$this->say('[System] Enabled blocking messages.', 'Error');
	$this->Submitlog('[System] Enabled blocking messages.');
}