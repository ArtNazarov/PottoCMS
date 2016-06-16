<?php
session_start();
$lang = 'rus';
$lang = $_POST['lang'];
$_SESSION['lang'] = $lang;
echo $lang;
?>
