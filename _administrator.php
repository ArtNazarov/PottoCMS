<?php 
define('APP', 0);
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/masterfactory/masterfactory.class.php';
$f = null;
$f = new MasterFactory($f);
$admin = $f->createInstance("Application", $params);
$admin->Run('Colibri', 'run')
?>