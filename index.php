<?php 
define('APP', 0);
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Core/ClassFactory/ClassFactory.class.php';
$f = null;
$f = new ClassFactory($f);
$readers = $f->createInstance("Application", $params, 'Core');
$readers->Run('PagesApplication', 'run', 'Separable');
