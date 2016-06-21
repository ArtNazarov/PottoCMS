<?php 
define('APP', 0);
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core/ClassFactory/ClassFactory.class.php';
$f = null;
$f = new ClassFactory($f);
$admin = $f->createInstance("Application", $params, 'Core');
$admin->Run('AdminTools', 'run', 'Separable');
?>