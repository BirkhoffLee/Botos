<?php
if(@$arguments[1] == 'pm' or @$arguments[1] == 'PM'){
	$this->say($this->SayName . ' 的 UUID: ' . $this->SayUID, 'Information', $this->SayName);
} else {
	$this->say($this->SayName . ' 的 UUID: ' . $this->SayUID, 'Information');
}