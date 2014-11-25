<?php
require 'config.php';
global $config;
$version = $config['version'];
$admin = $config['admin'];
$channel = $config['channel'];
$nick = $config['nick'];
$title = "Botos v{$version} running at {$channel}";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <title><?php echo $title;?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style type="text/css">
        body{
            margin-top: 50px;
            color: white;
            background-color: black;
            font-family: "Microsoft JhengHei";
            font-size: 20pt
        }
        #header{
            margin-left: 5%;
            margin-bottom: 20px;
            height: 10%;
            overflow: hidden
        }
        #left{
            height: 65%;
            width: 20%;
            float: left
        }
        #content{
            height: 65%;
            width: 80%;
            float: right
        }
        #title{
            font-size: 50pt;
            margin-right: 30px
        }
        #clock{
            color: white;
            height: 20%;
            text-align: right
        }
        #clock a{
            text-decoration: underline;
            color: white
        }
    </style>
    <script type="text/javascript">
        var now,hours,minutes,seconds,timeValue,copy;
        function showtime(){
            now = new Date();
            hours = now.getHours();
            minutes = now.getMinutes();
            seconds = now.getSeconds();
            copy = '<a href="http://opensource.org/licenses/MIT" target="_blank">MIT License</a>&nbsp;&nbsp;&nbsp;<a href="http://botos.irkhoff.com" target="_blank">Botos</a> by <a href="http://b.irkhoff.com" target="_blank">Birkhoff</a><br />';
            timeValue = (hours >= 12) ? copy + "下午 " : copy + "上午 ";
            timeValue += ((hours > 12) ? hours - 12 : hours) + ":";
            timeValue += ((minutes < 10) ? " 0" : "") + minutes + ":";
            timeValue += ((seconds < 10) ? " 0" : "") + seconds;
            document.getElementById("clock").innerHTML = timeValue;
	        document.ondragstart = function(){return false;}
		    document.oncontextmenu = function(){return false;}
		    document.onselectstart = function(){return false;}
            setTimeout("showtime()", 1000);
        }
        function status(status){
        	var headerInnerHTML = "<span id='title'>Botos</span><small>v<?php echo $version;?></small>&nbsp;&nbsp;&nbsp;暱稱: <?php echo $nick;?>&nbsp;&nbsp;&nbsp;頻道: <?php echo $channel;?>&nbsp;&nbsp;&nbsp;管理者: <?php echo $admin;?>&nbsp;&nbsp;&nbsp;狀態: ";
        	if(status == true){
        		document.getElementById("header").innerHTML = headerInnerHTML + "運行中";
        	} else {
        		document.getElementById("header").innerHTML = headerInnerHTML + "<font color='red'>已停止</font>";
        	}
        }
    </script>
    </head>
<body>
<div id='header'></div>
<div id='left'></div>
<div id='content'>
    <iframe src="main.php" onload="status(false)" scrolling="no" frameborder="0" width="100%" height="100%"></iframe>
</div>
<div id='clock'></div>
<script type="text/javascript">showtime();status(true)</script>
</body>
</html>