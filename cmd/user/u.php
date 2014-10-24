<?php
$url = 'https://www.googleapis.com/urlshortener/v1/url';
$data = json_encode(array("longUrl"=>$arguments[1]));
$options = array(
    'http' => array(
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => $data,
    ),
);
$context  = stream_context_create($options);
$result = json_decode(file_get_contents($url, false, $context), true);
$shortUrl = $result['id'];
$this->say($shortUrl);