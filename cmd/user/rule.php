<?php
/*
 * 回話規則設定
 * ~rule add             <say>            <response>
 * ~rule $arguments[1]   $arguments[2]    $arguments[3]
*/
$repliesJSON = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'replies.json';
if(@$arguments[1] == 'add' and isset($arguments[2]) and isset($arguments[3])){
	$say = $arguments[2];
	$response = $arguments[3];
    $replies = json_decode(file_get_contents($repliesJSON), true);
    if(isset($replies[$say])){
		$this->say('[回話規則設定] "' . $say . '": 此規則已經存在，請先刪除後再添加。', 'Error');
    } else {
    	$replies[$say] = $response;
    	$fn = fopen($repliesJSON, "w");
    	foreach ($replies as $key => $value) {
    		$ukey = urlencode($key);
    		$uvalue = urlencode($value);
    		$newReplies[$ukey] = $uvalue;
    	}
		fwrite($fn, urldecode(json_encode($newReplies)));
		fclose($fn);
		$this->say('[回話規則設定] "' . $say . '" 的回話內容: "' . $response . '"');
		$this->say('[回話規則設定] 規則添加完畢。');
    }
} elseif(@$arguments[1] == 'delete'){
	$say = $arguments[2];
    $replies = json_decode(file_get_contents($repliesJSON), true);
    if(!isset($replies[$say])){
		$this->say('[回話規則設定] "' . $say . '": 此規則不存在。', 'Error');
    } else {
		$this->say('[回話規則設定] "' . $say . '" 的回話內容: "' . $replies[$say] . '"');
    	unset($replies[$say]);
    	$fn = fopen($repliesJSON, "w");
    	foreach ($replies as $key => $value) {
    		$ukey = urlencode($key);
    		$uvalue = urlencode($value);
    		$newReplies[$ukey] = $uvalue;
    	}
		fwrite($fn, urldecode(json_encode($newReplies)));
		fclose($fn);
		$this->say('[回話規則設定] 規則刪除完畢。');
    }
} elseif(@$arguments[1] == 'list'){
    $replies = json_decode(file_get_contents($repliesJSON), true);
    if(!isset($replies)){
		$this->say('[回話規則] 無對話規則。', 'Error');
    } else {
		$this->say('[回話規則] =================規則清單開始===============');
    	$i = 0;
    	foreach ($replies as $key => $value) {
    		$i = $i + 1;
    		if($i == 4){
    			sleep(4);
    			$i = 0;
    		}
    		$value = str_replace('!!error#', '', str_replace('!!default#', '', $value));
    		$value = str_replace('!!notice#', '', str_replace('!!notify#', '', $value));
    		$value = str_replace('&**#', ' (換行) ', $value);
    		$this->say('[回話規則] "' . $key . '" 的回話內容: "' . $value . '"');
    	}
		$this->say('[回話規則] =================規則清單結束===============');
    }
} else {
	$this->say('參數錯誤! 用法:', 'Error');
	$this->say('~rule [command] (say) (response)', 'Error');
	$this->say('=================================', 'Error');
	$this->say('指令列表:', 'Error');
	$this->say('列出所有回話規則: ~rule list', 'Error');
	$this->say('新增回話規則: ~rule add <說話內容> <回話內容>', 'Error');
	$this->say('刪除回話規則: ~rule delete <說話內容>', 'Error');
	$this->say('=================================', 'Error');
}