<?php
/*
 *  botOS by Birkhoff
 *  http://b.irkhoff.com
 *  First release (2014/10/25 0:15)
 *
 *  ==License
 *  This script open to everybody,
 *  you can change everything in 
 *  this script, BUT DO NOT REMOVE 
 *  THE AUTHOR'S NAME AND URL!
 *  ==End License
 *
*/
$startTime = microtime(true);
define('DEBUG', true);
set_time_limit(0);
ini_set('display_errors', 'on');
if(DEBUG == true){
    error_reporting(E_ALL);
} else {
    error_reporting(0);
}
header("Content-type: text/html; charset=utf-8");
require 'config.php';

class botOS {
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
            global $config;
        	$this->Submitlog("botOS by Birkhoff {$config['version']} Started.");
            $this->Submitlog("=============================================");
            $this->Submitlog("Server: {$config['server']}:{$config['port']}");
            $this->Submitlog("BOT's nick name: {$config['nick']}");
            $this->Submitlog("Channel: {$config['channel']}");
            $this->Submitlog("The admin: {$config['admin']}");
            $this->Submitlog("=============================================");
            echo '<script type="text/javascript">function pageScroll(){window.scrollBy(0,3000);scrolldelay = setTimeout(\'pageScroll()\',100);}pageScroll();</script>';
            $this->socket = fsockopen($config['server'], $config['port']);
            $this->send_data('USER ' . $config['nick'] . ' b.irkhoff.com ' . $config['nick'] . ' :' . $config['name']);
            $this->send_data('NICK ' . $config['nick']);
            $this->send_data('JOIN ' . $config['channel']);
            $this->extraDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cmd' . DIRECTORY_SEPARATOR . 'extra' . DIRECTORY_SEPARATOR;
            $this->main();
        }

        public function Submitlog($log){
        	global $config;
            print('[' . date("Y-m-d H:i:s", mktime(date("H")+8, date("i"), date("s"), date("m"), date("d"), date("Y"))) . "] : $log <br />");
            ob_flush();
            flush();
            usleep(750);
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
                usleep(780);
                self::main();
        }

        private function _process_chat(){
            $arguments = @explode(' ', str_replace('   ', ' ', str_replace('  ', ' ', $this->SayContent)));
            $cmd = @str_replace("\\", '', str_replace('/', '', str_replace('.', '', str_replace('~', '', trim($arguments[0])))));
            $cmdDir = @dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cmd' . DIRECTORY_SEPARATOR;
            if(@$cmd != 'index'){
                if(filter_var($this->SayContent, FILTER_VALIDATE_URL)){
                    if(strpos($this->SayContent, '://goo.gl/')!==false){
                        include($this->extraDir . 'google_short_expand.php');
                    }
                    @include($this->extraDir . 'title_process.php');
                } elseif(substr($this->SayContent, 0, 1) == '~'){
                    global $config;
                    self::Submitlog('<strong>命令被解析: ' . $cmd . '</strong>');
                    if($this->SayName == $config['admin']){
                        $cmdFileName = $cmdDir . 'admin' . DIRECTORY_SEPARATOR . $cmd . '.php';
                        $this->arguments = $arguments;
                        if(file_exists($cmdFileName)){
                            include($cmdFileName);
                        }
                    }
                    if($this->shutup == false){
                        $cmdFileName = $cmdDir . 'user' . DIRECTORY_SEPARATOR . $cmd . '.php';
                        $this->arguments = $arguments;
                        if(file_exists($cmdFileName)){
                            include($cmdFileName);
                        }
                    }
                } else {
                    self::_reply();
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
                	self::say('[System] botOS ' . $config['version'] . ' is up.', 'Information', $config['admin']);
            	} elseif(substr($this->data, 0, 6) === 'PING :'){
                    $this->send_data(str_replace('PING :', 'PONG :', $this->data));
                } elseif(stripos($this->data, ' JOIN ' . $config['channel'])!==false){
                	$nameTemp = explode('!', $this->data);
                	$name = str_replace(':', '', $nameTemp[0]);
                    $ip = explode('@', $this->data);
                    $ip = explode(' ', $ip[1]);
                    $ip = $ip[0];
                    $query = @unserialize(file_get_contents('http://ip-api.com/php/' . $ip));
                    if($query && $query['status'] == 'success' && $name != $config['nick']) {
                        self::say('好久不見，來自 ' . $query['country'] . '-' . $query['city'] . ' 的 ' . $name . '!');
                    } elseif(strpos($this->data, 'gateway/')!==false){
                    	$ip = explode('ip.', $this->data);
                    	$ip = explode(' ', $ip[1]);
                    	$ip = $ip[0];
                    	$query = @unserialize(file_get_contents('http://ip-api.com/php/' . $ip));
                    	if($query && $query['status'] == 'success' && $name != $config['nick']) {
                        	self::say('好久不見，來自 ' . $query['country'] . '-' . $query['city'] . ' 的 ' . $name . '!');
                    	}
                    } elseif($name != $config['nick']){
                        self::say('好久不見，' . $name . '!');
                    }
                }
            }
        }

        private function _title_url_process(){
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
                self::say("[ {$titleTemp[0]} ] - $host", 'Default');
        	}
        }

        private function _reply(){
			global $config;
            $replies = json_decode(file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'replies.json'), true);
            foreach ($replies as $key => $value) {
            	if($this->SayContent == $key){
            		if(stripos($value, '!!error#') !== false){
						if(stripos($value, '&**#') !== false){
	            			$vvalue = explode('&**#', str_ireplace('!!error#', '', $value));
	            			foreach ($vvalue as $key => $wvalue) {
	            				self::say($wvalue, 'Error');
	            			}
	            		} else {
	            			self::say($value, 'Error');
	            		}
            		} elseif(stripos($value, '!!notice#') !== false){
						if(stripos($value, '&**#') !== false){
	            			$vvalue = explode('&**#', str_ireplace('!!notice#', '', $value));
	            			foreach ($vvalue as $key => $wvalue) {
	            				self::say($wvalue, 'Notice');
	            			}
	            		} else {
	            			self::say($value, 'Notice');
	            		}
            		} elseif(stripos($value, '!!notify#') !== false){
						if(stripos($value, '&**#') !== false){
	            			$vvalue = explode('&**#', str_ireplace('!!notify#', '', $value));
	            			foreach ($vvalue as $key => $wvalue) {
	            				self::say($wvalue, 'Notice');
	            			}
	            		} else {
	            			self::say($value, 'Notice');
	            		}
            		} elseif(stripos($value, '!!default#') !== false){
						if(stripos($value, '&**#') !== false){
	            			$vvalue = explode('&**#', str_ireplace('!!default#', '', $value));
	            			foreach ($vvalue as $key => $wvalue) {
	            				self::say($wvalue, 'Default');
	            			}
	            		} else {
	            			self::say($value, 'Default');
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
	        	$head = "<strong>{$config['nick']}</strong>: ";
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
            $this->SayContent = addslashes(htmlspecialchars(strip_tags(str_replace(' ' . $this->SayTarget . ' :', '', $SayContentTemp[1]))));
        }
}
$bot = new botOS();