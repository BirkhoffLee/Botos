<?php
if($arguments[1] == 'enable'){
	$this->disableBlock = false;
	$this->say('[System] Enabled blocking messages.', 'Error');
	$this->Submitlog('[System] Enabled blocking messages.');
} elseif($arguments[1] == 'disable'){
	$this->disableBlock = true;
	$this->say('[System] Disabled blocking messages.', 'Error');
	$this->Submitlog('[System] Disabled blocking messages.');
} elseif($arguments[1] == 'addkeyword'){
	global $root;
    $fileName = "{$root}blocks.json";
    $file = fopen($fileName, "r");
    $json = fread($file, filesize($fileName));
    fclose($file);
    $temp = json_decode($json, true);
    $blocks = $temp['blocks'];
    if(substr($blocks, -1) == ','){
    	$blocks = substr($blocks, 0, -1);
    }
    if(substr($blocks, 0, 1) == ','){
    	$blocks = substr($blocks, 1);
    }
    $blocks = str_replace(',,', '', $blocks);
    $blocks = array('blocks' => urlencode($blocks . ',' . $arguments[2]));

    $file = fopen($fileName, "w");
	fwrite($file, urldecode(json_encode($blocks)));
    $json = fread($file, filesize($fileName));
    fclose($file);
    self::say('[過濾規則] 關鍵字: ' . $arguments[2] . ' 添加完畢。', 'Error');
} elseif($arguments[1] == 'delkeyword'){
	global $root;
    $fileName = "{$root}blocks.json";
    $file = fopen($fileName, "r");
    $json = fread($file, filesize($fileName));
    fclose($file);
    $temp = json_decode($json, true);
    $arrBlocks = explode(',', $temp['blocks']);
    var_dump($json);
    foreach ($arrBlocks as $key => $value) {
    	if($value == $arguments[2]){
    		unset($arrBlocks[$key]);
    	}
    }
    $blocks = '';
    foreach ($arrBlocks as $key => $value) {
    	$blocks .= $value . ',';
    }
    if(substr($blocks, -1) == ','){
    	$blocks = substr($blocks, 0, -1);
    }

    $arrBlocks = array('blocks' => $blocks);

    $file = fopen($fileName, "w");
	fwrite($file, urldecode(json_encode($arrBlocks)));
    $json = fread($file, filesize($fileName));
    fclose($file);
    self::say('[過濾規則] 關鍵字: ' . $arguments[2] . ' 已刪除。', 'Error');
} elseif($arguments[1] == 'list'){
	global $root;
    $fileName = "{$root}blocks.json";
    $file = fopen($fileName, "r");
    $json = fread($file, filesize($fileName));
    fclose($file);
    $temp = json_decode($json, true);
    $this->say('[過濾清單] 含有關鍵字: ' . $temp['blocks']);
} else {
	$this->say('參數錯誤! 用法:', 'Error');
    $this->say('~block [command] (關鍵字)', 'Error');
	$this->say('=================================', 'Error');
	$this->say('指令列表:', 'Error');
	$this->say('啟用過濾訊息: ~block enable', 'Error');
    $this->say('停用過濾訊息: ~block disable', 'Error');
	$this->say('列出所有過濾關鍵字: ~block list', 'Error');
	$this->say('添加過濾暱稱關鍵字: ~block addkeyword <關鍵字>', 'Error');
	$this->say('刪除過濾暱稱關鍵字: ~block delkeyword <關鍵字>', 'Error');
	$this->say('=================================', 'Error');
}