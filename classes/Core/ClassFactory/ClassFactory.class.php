<?php
if (!defined('APP')) {die('ERROR ClassFactory.class.php');};
// Фабрика классов
class ClassFactory {
function __construct($params)
{
}

function createInstance($aClass, &$params, $aCategory='base')
{
	$some_obj = null;
	$some_obj = ucfirst(strtolower($aClass));
        if ($aCategory != 'base')
        {
            $class_path = $_SERVER['DOCUMENT_ROOT'] . "/classes/$aCategory/$aClass/$aClass.class.php";              
        }
        else
 {
   $class_path = $_SERVER['DOCUMENT_ROOT']. "classes/$aClass/$aClass.class.php";  
 };
	if (!class_exists($aClass, false)) // Если класс не подключен
	{ 	
	
	if (file_exists($class_path)) //Проверяем существование файла
		{
		require_once $class_path; 			
		}
		else
			{
			echo "Не удалось найти файл класса $aClass<br/>";
                        echo " по пути $class_path<br/>";
                        echo 'error in function ClassFactory->createInstance<br/>';
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
	foreach ($gears as $componentname => $classparams)
		{
	           $components["$componentname"] = $this->createInstance(
                           $classparams['classname'],
                           $params,
                           $classparams['category']);
		};
}
}

?>