<?php
$ip = $argument[1];
$query = @unserialize(file_get_contents('http://ip-api.com/php/' . $argument[1]));
if($query && $query['status'] == 'success') {
  $this->say('[IP位置查詢] IP: ' . $argument[1] . '國家: ' . $query['country'] . ' 城市：' . $query['city'].'!');
} else {
  $this->say('IP 資料獲取失敗', 'Error');
}