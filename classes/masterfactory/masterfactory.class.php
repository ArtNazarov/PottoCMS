<?php
if (!defined('APP')) {die('ERROR');};
// Фабрика классов
class MasterFactory {
function __construct($params)
{
}

function createInstance($aClass, &$params)
{
	$some_obj = null;
	$some_obj = ucfirst(strtolower($aClass));
	if (!class_exists($aClass, false)) // Если класс не подключен
	{ 	
	$class_path = $_SERVER['DOCUMENT_ROOT'].strtolower("/classes/$aClass/$aClass.class.php");
	if (file_exists($class_path)) //Проверяем существование файла
		{
		require_once $class_path; 			
		}
		else
			{
			echo "Не удалось найти файл класса $aClass";
			exit;
			die();
			};
	};
	$some_obj = new $aClass($params);
	return $some_obj;
}
// Добавляет в набор компонентов объекты - конвеер
function createInstances(&$components, $gears, &$params)
{
	foreach ($gears as $componentname => $classname)
		{
	           $components["$componentname"] = $this->createInstance("$classname", $params);
		};
}
}

?>