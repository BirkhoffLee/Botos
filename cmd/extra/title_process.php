<?php
if(stripos($this->SayContent, 'http://') !== false){
    $host = str_ireplace('http://', '', $this->SayContent);
    $url = $this->SayContent;
} elseif(stripos($this->SayContent, 'https://') !== false){
    $host = str_ireplace('https://', '', $this->SayContent);
    $url = $this->SayContent;
} else {
    $host = $this->SayContent;
    if(substr($host, -1) == '/'){
        $host = substr($host, 0, -1);
    }
    $url = 'http://' . $host;
}
if(stripos($host, '/') !== false){
    $host = explode('/', $host);
    $host = $host[0];
} elseif(stripos($host, '\\') !== false){
    $host = explode('\\', $host);
    $host = $host[0];
} else {
    $host = $url;
}

if(substr($url, -1) == '#'){
    $url = substr($url, 0, -1);
}
if (filter_var($url, FILTER_VALIDATE_URL) !== FALSE) {
	$fsock = @fsockopen(str_ireplace('https://', '', str_ireplace('http://', '', $host)), 80, $errno, $errstr, 6);
    if($fsock){
		$this->Submitlog('URL 分析中: ' . $url);
		ini_set("user_agent","Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.104 Safari/537.36");
		if($str = file_get_contents($url,FALSE,NULL,0,2048)){
		    $titleTemp = explode('<title', $str);
		    $titleTemp = explode('>', $titleTemp[1]);
		    $titleTemp = explode('<', $titleTemp[1]);
			if($titleTemp[0] != ''){
    			$title = str_replace("\n", '', trim($titleTemp[0]));
		    	$this->Submitlog('URL 解析完成: ' . $url . '，截取了 2048 字節');
		    	$this->say("[ {$title} ] - $host", 'Default');
		    }
		}
	}
}