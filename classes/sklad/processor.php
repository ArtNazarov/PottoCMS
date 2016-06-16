<?php
// define('APP', 0);
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/sklad/sklad.class.php';
$p = null;
$sklad = new Sklad();
$sklad->ProcessDb();
?>
