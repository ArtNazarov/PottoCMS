<?php
$v_ip = $_SERVER['REMOTE_ADDR'];
$ag = $_SERVER['HTTP_USER_AGENT'];
$ru = $_SERVER['REQUEST_URI'];
$v_date = date("l d F H:i:s"); 
$fp = fopen("./logs/ips.log", "a+");
fputs($fp, "IP:$v_ip;REQUEST:$ru;DATE:$v_date;USERAGENT:$ag;HITS:$hits#\n\r\n\r");
fclose($fp);
?>
