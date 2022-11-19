<?php 
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(E_ALL);
define('APP', 1);
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Core/ClassFactory/ClassFactory.class.php';
$f = [];
$f = new ClassFactory($f);
$initParams = [];
$readers = $f->createInstance("Application", $initParams, 'Core');
$readers->Run('PagesApplication', 'run', 'Separable');
