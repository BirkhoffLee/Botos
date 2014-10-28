<?php
/*
 * 回話規則設定
 * ~rule add             <say>            <response>
 * ~rule $arguments[1]   $arguments[2]    $arguments[3]
*/
global $config;
$repliesJSON = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'replies.json';
$arguments[1] = strtolower($arguments[1]);
if(@$arguments[1] == 'add' and isset($arguments[2]) and isset($arguments[3])){
	$say = str_ireplace('$SP', ' ', $arguments[2]);
	$argu = $arguments;
    unset($argu[0]);
    unset($argu[1]);
    unset($argu[2]);
    foreach ($argu as $key => $value) {
        @$response .= $value . ' '; 
    }
    $response = trim($response);
    if(strpos(substr($response, 0, 1)) == '~'){
        $this->say('[回話規則設定] "' . $say . '": 此規則與指令衝突！', 'Error');
    }
    $replies = json_decode(file_get_contents($repliesJSON), true);

    global $cmdDir;
    $adminCMDfn = $cmdDir . 'admin' . DIRECTORY_SEPARATOR . str_replace('~', '', $say) . '.php';
    $userCMDfn = $cmdDir . 'user' . DIRECTORY_SEPARATOR . str_replace('~', '', $say) . '.php';

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
} elseif(@$arguments[1] == 'delete' and $this->SayName == $config['admin']){
    $say = str_ireplace('$SP', ' ', $arguments[2]);
    $replies = json_decode(file_get_contents($repliesJSON), true);
    if(!isset($replies[$say])){
        $this->say('[回話規則設定][管理指令] "' . $say . '": 此規則不存在。', 'Error');
    } else {
        $this->say('[回話規則設定][管理指令] "' . $say . '" 的回話內容: "' . $replies[$say] . '"');
        unset($replies[$say]);
        $fn = fopen($repliesJSON, "w");
        foreach ($replies as $key => $value) {
            $ukey = urlencode($key);
            $uvalue = urlencode($value);
            $newReplies[$ukey] = $uvalue;
        }
        fwrite($fn, urldecode(json_encode($newReplies)));
        fclose($fn);
        $this->say('[回話規則設定][管理指令] 規則刪除完畢。');
    }
} elseif(@$arguments[1] == 'look'){
    $say = str_ireplace('$SP', ' ', $arguments[2]);
    $replies = json_decode(file_get_contents($repliesJSON), true);
    if(!isset($replies[$say])){
        $this->say('[回話規則設定] "' . $say . '": 此規則不存在。', 'Error');
    } else {
        $this->say('[回話規則設定] "' . $say . '" 的回話內容: "' . $replies[$say] . '"');
    }
} elseif(@$arguments[1] == 'list'){
    $this->say($this->SayName . ': 清單將用悄悄話方式傳送。', 'Error');
    $replies = json_decode(file_get_contents($repliesJSON), true);
    if(!isset($replies)){
		$this->say('[回話規則] 無對話規則。', 'Error', $this->SayName);
    } else {
		$this->say('[回話規則] =================規則清單開始===============', 'Information', $this->SayName);
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
    		$this->say('[回話規則] "' . $key . '" 的回話內容: "' . $value . '"', 'Information', $this->SayName);
    	}
		$this->say('[回話規則] =================規則清單結束===============', 'Information', $this->SayName);
    }
} else {
    $this->say($this->SayName . ': 參數錯誤！幫助訊息已使用悄悄話傳送。', 'Error');
	$this->say('參數錯誤! 用法:', 'Error', $this->SayName);
    $this->say('~rule [command] (說話內容) (回話內容)', 'Error', $this->SayName);
    $this->say('P.S: (說話內容) 參數中的空格請用 $SP 代替', 'Error', $this->SayName);
    $this->say('P.S: (說話內容) 參數中的換行請用 &**# 代替', 'Error', $this->SayName);
    $this->say('P.S: (說話內容) 紅色字體請在句尾加上 !!error#', 'Error', $this->SayName);
    $this->say('P.S: (說話內容) 灰色字體請在句尾加上 !!notice#', 'Error', $this->SayName);
    $this->say('P.S: (說話內容) 黑色字體請在句尾加上 !!default#', 'Error', $this->SayName);
	$this->say('=================================', 'Error', $this->SayName);
	$this->say('指令列表:', 'Error', $this->SayName);
    sleep(10);
	$this->say('列出所有回話規則 [悄悄話方式發送]: ~rule list', 'Error', $this->SayName);
    $this->say('列出指定回話規則: ~rule look <說話內容>', 'Error', $this->SayName);
	$this->say('新增回話規則: ~rule add <說話內容> <回話內容>', 'Error', $this->SayName);
	$this->say('刪除回話規則 [管理指令]: ~rule delete <說話內容>', 'Error', $this->SayName);
	$this->say('=================================', 'Error', $this->SayName);
}