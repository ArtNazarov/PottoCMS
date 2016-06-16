<?php 
define('APP', 0);
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/masterfactory/masterfactory.class.php';
$f = null;
$f = new MasterFactory($f);
$readers = $f->createInstance("Application", $params);
$readers->Run('Jerboa', 'run')
?>