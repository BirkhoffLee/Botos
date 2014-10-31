<?php
$result = json_decode(file_get_contents('https://www.googleapis.com/urlshortener/v1/url?shortUrl=' . $this->SayContent), true);
if(@$result['status'] == 'OK'){
	$this->say($this->SayContent . ' 的原網址: ' . $result['longUrl']);
}