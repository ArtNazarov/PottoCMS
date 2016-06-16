<?php
define('APP', 0);
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/berkut/berkut.class.php';
$p = null;
$I = new Berkut($p);
$I->captcha(80, 30, 'hello!')
?>
