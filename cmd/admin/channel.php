<?php
if(isset($arguments[1])){
    global $root;
    global $config;
    $fileName = "{$root}config.php";
    $file = fopen($fileName, "r");
    $content = fread($file, filesize($fileName));
    fclose($file);
    if(substr($arguments[1], 0, 1) != '#'){
        $newChannel = '#' . $arguments[1];
    } else {
        $newChannel = $arguments[1];
    }
	$content = str_replace($config['channel'], $newChannel, $content);
    $fileName = "{$root}config.php";
    $file = fopen($fileName, "w");
    fwrite($file, $content);
    fclose($file);

    $this->say('[System] botOS 將重新啟動以更換頻道至 ' . $newChannel . '。', 'Error', $config['channel']);
    $this->say('[System] botOS is restarting by ' . $this->SayName . '\'s permission.', 'Error', $config['channel']);
    $this->say('[System] botOS is restarting.', 'Error', $config['admin']);
    $this->send_data('QUIT', 'Restart');
    echo "<meta http-equiv=\"refresh\" content=\"0.1\">";
    exit;
} else {
    $this->say('參數錯誤! 用法: ~channel <頻道名>', 'Error');
}