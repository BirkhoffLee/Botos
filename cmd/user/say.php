<?php
if($arguments[1] != ''){
	$this->say($this->SayName . ' 表示: ' . str_replace($arguments[0], '', $this->SayContent));
} else {
	$this->say('參數錯誤! 用法: ~say <內容>', 'Error');
}