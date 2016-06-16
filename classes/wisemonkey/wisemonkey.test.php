<?php
define('APP', 0);
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/wisemonkey/wisemonkey.class.php';
$opts = new WiseMonkey;
$opts->setOption('test', 'test');
echo $opts->getOption('test');
$opts->setOption('test', 'tst');
echo $opts->getOption('test');
?>