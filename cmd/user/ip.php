<?php
global $config;
if($arguments[1] == ''){
	$this->say('參數錯誤！用法：~ip <ipaddress>', 'Error');
} else {
	$ip = explode('://', trim($arguments[1]));
	$ip = str_ireplace('/', '', str_ireplace('\\', '', $ip[0]));
	$query = @unserialize(file_get_contents('http://ip-api.com/php/' . $ip));
	if(@$query && @$query['status'] == 'success') {
		if(@$query['country'] == ''){
			$this->say('IP 資料獲取失敗', 'Error');
		} else {
			if(@$query['city'] == ''){
				$Location = ' 國家: ' . $query['country'];
			} else {
				$Location = ' 國家: ' . $query['country'] . ' 城市: ' . $query['city'];
			}
		    foreach ($config['ipLocations'] as $key => $value) {
		        $Location = str_ireplace($key, $value, $Location);
		    }
			$this->say('[IP位置查詢] IP: ' . $ip . $Location);
		}
	} else {
		$this->say('IP 資料獲取失敗', 'Error');
	}
}