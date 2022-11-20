<?php 
//echo "Запуск приложения администратора;";
define('APP', 1);
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core/ClassFactory/ClassFactory.class.php';
$f = [];
// Новая фабрика классов
$f = new ClassFactory($f);
  $initialParams = [];
$admin = $f->createInstance("Application", $initialParams, 'Core');
 $admin->Run('AdminTools', 'run', 'Separable');
//echo "Приложение завершено";
?>