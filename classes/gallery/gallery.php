<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/gallery/gallery.class.php';
$p = null;
$gallery = new Gallery($p);
echo  $gallery->run();
?>
