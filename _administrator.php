<?php 
//echo "Запуск приложения администратора;";
define('APP', 1);
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core/ClassFactory/ClassFactory.class.php';
$f = [];
// Новая фабрика классов
$f = new ClassFactory($f);
//echo "Создаем экземпляр класса Application;";
echo "Создаем фабрику классов";
$initialParams = [];
$admin = $f->createInstance("Application", $initialParams, 'Core');
//echo "Вызываем приложение администратора";
echo "Запуск админки";
$admin->Run('AdminTools', 'run', 'Separable');
//echo "Приложение завершено";
?>