<?php
global $startTime;
global $config;
$time = round(microtime(true) - $startTime);
$value = array( 
  "years" => 0, "days" => 0, "hours" => 0, 
  "minutes" => 0, "seconds" => 0, 
); 
if($time >= 31556926){ 
  $value["years"] = floor($time/31556926); 
  $time = ($time%31556926); 
} 
if($time >= 86400){ 
  $value["days"] = floor($time/86400); 
  $time = ($time%86400); 
} 
if($time >= 3600){ 
  $value["hours"] = floor($time/3600); 
  $time = ($time%3600); 
} 
if($time >= 60){ 
  $value["minutes"] = floor($time/60); 
  $time = ($time%60); 
} 
$value["seconds"] = floor($time);
$uptime = "{$value['days']} 天 {$value['hours']} 小時 {$value['minutes']} 分鐘 {$value['seconds']} 秒";
$this->say('botOS by Birkhoff >>>>>>>>>>>>>>>>>>>>>資訊');
$this->say('===========================================');
$this->say("連線至伺服器: {$config['server']}:{$config['port']}");
$this->say("機器人的暱稱: {$config['nick']}");
$this->say("我所在的頻道: {$config['channel']}");
$this->say("機器人管理員: {$config['admin']}");
$this->say("目前運行時間: {$uptime}");
$this->say('===========================================');