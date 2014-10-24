<?php
if(!$this->shutup){
	$this->say($this->SayName . ': 好啦，不吵惹啦，齁', 'Error');
	$this->Submitlog('<span style="color:red">SHUT UP MODE ON.</span>');
	$this->shutup = true;
} else {
	$this->shutup = false;
	$this->say($this->SayName . ': 呼，輕鬆多惹', 'Error');
	$this->Submitlog('<span style="color:red">SHUT UP MODE OFF.</span>');
}