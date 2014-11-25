<?php
$startTime = microtime(true);
$root = dirname(__FILE__) . DIRECTORY_SEPARATOR;
define('DEBUG', true);
set_time_limit(0);
if(DEBUG == true){
    ini_set('display_errors', 'on');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 'off');
    error_reporting(0);
}
header("Content-type: text/html; charset=utf-8");
require 'config.php';

class Botos {
        var $socket;
        var $data;
        var $serverMessages;
        var $SayName;
        var $SayCMD;
        var $SayTarget;
        var $SayContent;
        var $shutup = false;
        var $standby = false;
        var $disableBlock;
        var $arguments;
        var $extraDir;

        public function __construct(){
            self::initalize();
        }

        private function initalize(){
            $this->extraDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cmd' . DIRECTORY_SEPARATOR . 'extra' . DIRECTORY_SEPARATOR;
            require $this->extraDir. 'main.header.php';
            global $config;
            $this->Submitlog("Botos by Birkhoff Version {$config['version']} Started.");
            $this->Submitlog("=============================================");
            $this->Submitlog("Server: {$config['server']}:{$config['port']}");
            $this->Submitlog("Nickname: {$config['nick']}");
            $this->Submitlog("Channel: {$config['channel']}");
            $this->Submitlog("Admin: {$config['admin']}");
            $this->Submitlog("=============================================");
            echo '<script type="text/javascript">function pageScroll(){window.scrollBy(0,3000);scrolldelay = setTimeout(\'pageScroll()\',100);}pageScroll();</script>';
            $this->socket = @fsockopen($config['server'], $config['port']) or die('<h2>ERROR: Failed connecting to ' . $config['server'] . '</h2>');
            $this->send_data('USER ' . $config['nick'] . ' botos.irkhoff.com ' . $config['nick'] . ' :' . $config['name']);
            $this->send_data('NICK ' . $config['nick']);
            $this->send_data('JOIN ' . $config['channel']);
            
            if($config['checkUpdate']){
                require_once($this->extraDir . 'update.php');
            }

            $this->main();
        }

        public function Submitlog($log){
            echo '[' . date("Y-m-d H:i:s", mktime(date("H")+8, date("i"), date("s"), date("m"), date("d"), date("Y"))) . "] : $log <br />";
            ob_flush();
            flush();
            usleep(100);
        }

        public function main(){
        		global $config;
                $this->data = str_replace("\r\n", '', trim(fgets($this->socket, 256)));

                if(!isset($this->data) or $this->data == null or $this->data == ''){
					self::main();
				}

                if(strpos($this->data, 'Nickname is already in use.') !== false){
                	self::Submitlog("Received ServerMessage -> <strong><span style='color:#3b5998'>{$this->data}</span></strong>");
                	self::Submitlog('<strong>登入失敗：上次登入 Session 尚未過期或暱稱被盜用。</strong>');
                	exit;
                }

                $checkChating = explode(' ', $this->data);
                if(@$checkChating[1] != 'PRIVMSG'){
                    // 非聊天階段
                    self::_serv_msg();
                } else {
                    // 聊天階段
                    global $config;
        			self::_parse();
                    
                    if(!isset($this->SayName) or !isset($this->SayTarget) or !isset($this->SayUID) or !isset($this->SayContent) or $this->SayName == '' or $this->SayTarget == '' or $this->SayUID == '' or $this->SayContent == ''){
                    	// 資料錯誤
                    	self::Submitlog("<span style='color:red'><strong>Error with parsing data information!</strong></span>");
                    }

                    self::Submitlog("<strong>[CHAT] {$this->SayName}</strong>: {$this->SayContent}");
                    self::_process_chat();
            	}
                self::main();
        }

        private function _process_chat(){
            $arguments = @explode(' ', str_replace('   ', ' ', str_replace('  ', ' ', $this->SayContent)));
            $cmd = @str_replace("\\", '', str_replace('/', '', str_replace('.', '', str_replace('~', '', trim($arguments[0])))));
            $cmdDir = @dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cmd' . DIRECTORY_SEPARATOR;
            if(@$cmd != 'index'){
                if(substr($this->SayContent, 0, 1) == '~'){
                    global $config;
                    $adminCMDfn = $cmdDir . 'admin' . DIRECTORY_SEPARATOR . $cmd . '.php';
                    $userCMDfn = $cmdDir . 'user' . DIRECTORY_SEPARATOR . $cmd . '.php';
                    if(file_exists($adminCMDfn) and $this->SayName == $config['admin']){
                        self::Submitlog('<strong>管理員命令被解析: ' . $cmd . '</strong>');
                        $this->arguments = $arguments;
                        include($adminCMDfn);
                    } elseif(file_exists($userCMDfn)){
                        self::Submitlog('<strong>使用者命令被解析: ' . $cmd . '</strong>');
                        $this->arguments = $arguments;
                        include($userCMDfn);
                    } else {
                        self::say($this->SayName . ': 找不到指令！請送出 ~help 以查看指令列表。', 'Error');
                    }
                } else {
                    self::_reply();
                }
                if(strpos($this->SayContent, '://goo.gl/')!==false){
                    include($this->extraDir . 'google_short_expand.php');
                } else {
                    include($this->extraDir . 'title_process.php');
                }
            }
        }

        private function _serv_msg(){
        	if($this->data != '' and $this->data != null and $this->data != ' '){
        		global $config;
            	self::Submitlog("Received ServerMessage -> <strong><span style='color:#3b5998'>{$this->data}</span></strong>");
            	if(stripos($this->data, 'End of /NAMES list') !== false and $this->standby == false){
            		$this->standby = true;
                	self::say('Bot 連線成功', 'Notice');
                	self::say('[System] Botos ' . $config['version'] . ' is up.', 'Information', $config['admin']);
            	} elseif(substr($this->data, 0, 6) === 'PING :'){
                    $this->send_data(str_replace('PING :', 'PONG :', $this->data));
                } elseif(stripos($this->data, ' JOIN ' . $config['channel'])!==false){
                	$nameTemp = explode('!', $this->data);
                	$name = str_replace(':', '', $nameTemp[0]);
                    $ip = @explode('@', $this->data);
                    $ip = @explode(' ', $ip[1]);
                    $ip = @$ip[0];
                    $query = @unserialize(file_get_contents('http://ip-api.com/php/' . $ip));
                    if($query && $query['status'] == 'success' && $name != $config['nick'] && $query['country']!='' && $query['city'] != '') {                            $Location = $query['country'] . ', ' . $query['city'];
                        $Location = $query['country'] . ', ' . $query['city'];
                        foreach ($config['ipLocations'] as $key => $value) {
                            $Location = str_ireplace($key, $value, $Location);
                        }
                        self::say('好久不見，來自 ' . $Location . ' 的 ' . $name . '!', $config['channel']);
                    } elseif(strpos($this->data, 'gateway/')!==false){
                    	$ip = @explode('ip.', $this->data);
                    	$ip = @explode(' ', $ip[1]);
                    	$ip = @$ip[0];
                    	$query = @unserialize(file_get_contents('http://ip-api.com/php/' . $ip . '?fields=country,city,query,status,message'));
                    	if($query && $query['status'] == 'success' && $name != $config['nick'] && $query['country']!='' && $query['city'] != '') {
                            $Location = $query['country'] . ', ' . $query['city'];
                            foreach ($config['ipLocations'] as $key => $value) {
                                $Location = str_ireplace($key, $value, $Location);
                            }
                        	self::say('好久不見，來自 ' . $Location . ' 的 ' . $name . '!', $config['channel']);
                    	}
                    } elseif($name != $config['nick']){
                        self::say('好久不見，' . $name . '!', $config['channel']);
                    }
                }
            }
        }

        private function _reply(){
			global $config;
			$fn = fopen(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'replies.json', 'r');
			$cont = fread($fn, filesize(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'replies.json'));
			fclose($fn);
            $replies = json_decode($cont, true);
            foreach ($replies as $key => $value) {
            	if(strtolower($this->SayContent) == strtolower($key)){
            		if(stripos($value, '!!error#') !== false){
						if(stripos($value, '&**#') !== false){
	            			$vvalue = explode('&**#', str_ireplace('!!error#', '', $value));
	            			foreach ($vvalue as $key => $wvalue) {
	            				self::say($wvalue, 'Error');
	            			}
	            		} else {
	            			self::say(str_ireplace('!!error#', '', $value), 'Error');
	            		}
            		} elseif(stripos($value, '!!notice#') !== false){
						if(stripos($value, '&**#') !== false){
	            			$vvalue = explode('&**#', str_ireplace('!!notice#', '', $value));
	            			foreach ($vvalue as $key => $wvalue) {
	            				self::say($wvalue, 'Notice');
	            			}
	            		} else {
	            			self::say(str_ireplace('!!notice#', '', $value), 'Notice');
	            		}
            		} elseif(stripos($value, '!!notify#') !== false){
						if(stripos($value, '&**#') !== false){
	            			$vvalue = explode('&**#', str_ireplace('!!notify#', '', $value));
	            			foreach ($vvalue as $key => $wvalue) {
	            				self::say($wvalue, 'Notice');
	            			}
	            		} else {
	            			self::say(str_ireplace('!!notify#', '', $value), 'Notice');
	            		}
            		} elseif(stripos($value, '!!default#') !== false){
						if(stripos($value, '&**#') !== false){
	            			$vvalue = explode('&**#', str_ireplace('!!default#', '', $value));
	            			foreach ($vvalue as $key => $wvalue) {
	            				self::say($wvalue, 'Default');
	            			}
	            		} else {
	            			self::say(str_ireplace('!!default#', '', $value), 'Default');
	            		}
	            	} elseif(stripos($value, '&**#') !== false){
	            			$vvalue = explode('&**#', $value);
	            			foreach ($vvalue as $key => $wvalue) {
	            				self::say($wvalue);
	            			}
            		} else {
            			self::say($value);
            		}
            	}
            }
        }

        public function say($msg, $type = 'Information', $target = null){
        	global $config;
        	if($this->shutup == false){
	        	if($this->SayTarget == $config['nick'] and $target == null){
	        		$target = $this->SayName;
	        	} elseif($target == null){
	        		$target = $config['channel'];
	        	}
	        	$head = "<strong>{$config['nick']} => {$target}</strong>: ";
	        	switch ($type){
	                case "Notify":
	                case "Notice":
	                	self::Submitlog("$head<span style='color:#C0C0C0'>{$msg}</span>");
	                    self::send_data("PRIVMSG $target :\x0314" . $msg, false);
	                    break;
	                case "Error":
	                	self::Submitlog("$head<span style='color:#FF0000'>{$msg}</span>");
	                    self::send_data("PRIVMSG $target :\x0304" . $msg, false);
	                    break;
	                case "Default":
	                	self::Submitlog("$head{$msg}");
	                    self::send_data("PRIVMSG $target :" . $msg, false);
	                    break;
	                default: //Information
	                	self::Submitlog("$head<span style='color:#000080'>{$msg}</span>");
	                    self::send_data("PRIVMSG $target :\x0302" . $msg, false);
	                    break;
	            }	
        	}
        }

        public function send_data($cmd, $log = true){
            fputs($this->socket, $cmd . "\r\n");
            if($log){
                self::Submitlog("Sent command -> <strong>$cmd</strong>");
            }
        }

        private function _parse(){
        	global $config;
        	$this->serverMessages = explode(' ', $this->data);
            $this->SayName = explode('!', $this->serverMessages[0]);
            $this->SayName = substr($this->SayName[0],1,strlen($this->SayName[0]));

            if(!$this->disableBlock){
                $fileName = dirname(__FILE__) . DIRECTORY_SEPARATOR . "blocks.json";
                $file = fopen($fileName, "r");
                $json = fread($file, filesize($fileName));
                fclose($file);
	            $temp = json_decode($json, true);
                $blocks = explode(',', $temp['blocks']);
	            foreach ($blocks as $key => $value) {
	            	if(stripos($this->SayName, $value)!==false){
	            		self::Submitlog("<span style='color:red'>Blocked message from {$this->SayName}.</span>");
	            		self::main();
	            	}
	            }
            }
            $this->SayTarget = $this->serverMessages[2];
            $UID_temp = explode('!', $this->data);
            $UID_temp = explode(' ', $UID_temp[1]);
            $this->SayUID = $UID_temp[0];
            $SayContentTemp = explode('PRIVMSG', $this->data);
            $this->SayContent = htmlspecialchars(strip_tags(str_replace(' ' . $this->SayTarget . ' :', '', $SayContentTemp[1])));
        }
}
$bot = new Botos();