<?php
$this->say($this->SayName . ': 幫助指令已使用悄悄話方式傳送。');
$this->say('botOS by Birkhoff >>>>>>>>>幫助', 'Information', $this->SayName);
$this->say('===============================', 'Information', $this->SayName);
$this->say('~help - 幫助指令', 'Information', $this->SayName);
usleep(6000);
$this->say('~info - 顯示 botOS 運行狀況', 'Information', $this->SayName);
$this->say('~say <content> - 講話', 'Information', $this->SayName);
$this->say('~ip <ip> - 查詢 IP 地理位置資訊', 'Information', $this->SayName);
$this->say('~version - 顯示版本訊息', 'Information', $this->SayName);
$this->say('~g/~google - 查詢資料', 'Information', $this->SayName);
usleep(6000);
$this->say('~uid [pm] - 顯示你的 UID (pm:悄悄話)', 'Information', $this->SayName);
$this->say('~url/u <URL> - 產生短網址', 'Information', $this->SayName);
$this->say('~shutup - 安靜模式', 'Information', $this->SayName);
$this->say('~rand <x> <y> - 產生 x~y 內的亂數', 'Information', $this->SayName);
usleep(5000);
$this->say('~rule - 請送出指令 ~rule 查看幫助', 'Information', $this->SayName);
$this->say('~uptime - 查看 botOS 運行時間', 'Information', $this->SayName);
$this->say('===============================', 'Information', $this->SayName);