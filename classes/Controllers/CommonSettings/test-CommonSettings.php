<?php
define('APP', 0);
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core/CommonSettings/CommonSettings.class.php';
$params = null;
$opts = new CommonSettings($params);
$opts->setOption('test', 'test');
echo $opts->getOption('test');
$opts->setOption('test', 'tst');
echo $opts->getOption('test');
