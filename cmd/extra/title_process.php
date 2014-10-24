<?php
$url = $this->SayContent;
$urlParse = parse_url($url);
if(isset($urlParse['query'])){
    $query = '?' . $urlParse['query'];
} else {
    $query = '';
}
$url = $urlParse['scheme'] . '://' . $urlParse['host'] . $urlParse['path'] . $query;
ini_set("user_agent","Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.104 Safari/537.36");
if($str = file_get_contents($this->SayContent)){
    $titleTemp = explode('<title', $str);
    $titleTemp = explode('>', $titleTemp[1]);
    $titleTemp = explode('<', $titleTemp[1]);
    $urlTemp = explode('\\', str_replace('https://', '', str_replace('http://', '', $this->SayContent)));
    if(strpos($urlTemp[0], '/')!==false){
        $temp = explode('/', $urlTemp[0]);
        $host = $temp[0];
    }
    if($titleTemp[0] != '' and $host != ''){
    	$this->say("[ {$titleTemp[0]} ] - $host", 'Default');
    }
}