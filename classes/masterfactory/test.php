<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/masterfactory/masterfactory.class.php';
$params = NULL;
$F = new MasterFactory($params);
$T = $F->createInstance("Unknown", $params);
?>