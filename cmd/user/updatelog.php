<?php
global $config;
$nowVersion = explode(" ", $config['version']);
$nowVersion = $nowVersion[0];

$updateServer = 'http://botos.irkhoff.com';
$updateCheck = $updateServer . '/update/latest.txt';
$updateLog = $updateServer . '/update/updatelog/' . $nowVersion . '.txt';

if($this->SayTarget != $config['nick']){
	$this->say($this->SayName . ': 更新日誌已使用悄悄話方式傳送。');
}
$this->say('更新日誌    ' . $config['version'], 'Information', $this->SayName);
$this->say('===============================', 'Information', $this->SayName);
$fp = fopen($updateLog,'r');
while(!feof($fp)){
    $buffer = iconv('big5', 'utf-8', fgets($fp, 4096));
    $this->say($buffer, 'Information', $this->SayName);
}
fclose($fp);
$this->say('===============================', 'Information', $this->SayName);