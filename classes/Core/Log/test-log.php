<?php
define('APP', 0);
// testing class
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/log/log.class.php';
$params = null;
echo "<h1> Must return list for 3 messages </h1>";
$log = new Log($params);
$log->ClearLog('test-log');
$log->WriteLog('test-log', 'test-log.php message 1' . "\r\n");
$log->WriteLog('test-log', 'test message 2' . "\r\n");
$log->WriteLog('test-log', 'test message 3' . "\r\n");
$j = $log->ReadLog('test-log');
echo $j;
