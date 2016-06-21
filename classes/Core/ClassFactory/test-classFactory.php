<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/ClassFactory/ClassFactory.class.php';
$params = NULL;
$F = new ClassFactory($params);
$T = $F->createInstance("Unknown", $params);
?>