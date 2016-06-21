<?php
define('APP', 0);
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/berkut/berkut.class.php';
$berkut = new Berkut(null);
$berkut->captcha(150, 31,  urldecode(mcrypt_ecb(MCRYPT_DES, $_GET['key'], $_GET['text'], MCRYPT_DECRYPT)));
?>