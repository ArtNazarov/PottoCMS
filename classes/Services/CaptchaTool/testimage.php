<?php
define('APP', 0);
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Services/CaptchaTool/CaptchaTool.class.php';
$p = null;
$I = new CaptchaTool($p);
$I->captcha(80, 30, 'hello!')
?>
