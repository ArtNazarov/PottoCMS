<?php 
//echo "Запуск приложения администратора;";
define('APP', 1);
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core/ClassFactory/ClassFactory.class.php';
$f = null;
$f = new ClassFactory($f);
//echo "Создаем экземпляр класса Application;";
$admin = $f->createInstance("Application", $params, 'Core');
//echo "Вызываем приложение администратора";
$admin->Run('AdminTools', 'run', 'Separable');
//echo "Приложение завершено";
?>