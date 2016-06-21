<?php
define('APP', 0);
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Services/CaptchaTool/CaptchaTool.class.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Helpers/Encryption/Encryption.class.php';
$ct = new CaptchaTool(null);
$enc = new Encryption();
$ct->captcha(150, 31,  urldecode($enc->decrypt_data($_GET['key'], $_GET['text'])));
?>