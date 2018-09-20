<?php 
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(E_ALL);
define('APP', 1);
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Core/ClassFactory/ClassFactory.class.php';
$f = null;
$f = new ClassFactory($f);
$readers = $f->createInstance("Application", $params, 'Core');
$readers->Run('PagesApplication', 'run', 'Separable');
