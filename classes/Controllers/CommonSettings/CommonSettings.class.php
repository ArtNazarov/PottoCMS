<?php
if (!defined('APP')) {die('ERROR wisemonkey.class.php');};
/* 
 * Модуль Общих настроек сайта
 * Требует наличия таблицы prefix__options следующей структуры
 * 

CREATE TABLE IF NOT EXISTS `homepage__options` (
  `optname` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `optvalue` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`optname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

Настройки модулей ключ module_option.
Зависит от кэширования и базы данных. 
 */
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core/DatabaseLayer/DatabaseLayer.class.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core/CacheLayer/CacheLayer.class.php';
class CommonSettings
{
    /*
     * База данных
     */
var $m;
/*
 * Опции
 */
var $ops;
/*
 * Кэш
 */
var $cache;
    
 /*
  * Конструктор.
  * Подключается к базе данных, создает объект кэша, обновляет опции
  */
    function __construct($params)
	{
     $this->m = new DatabaseLayer($params);
	 $this->cache = new CacheLayer($params);
 	 $this->m->Plug();
	 $this->refresh();
	}
/*
 * Деструктор. Отключается от базы, удаляет опции
 */        
     function __destruct()
     {
     $this->m->Clear();
     $this->m->Done();
     $this->m->__destruct();
     unset($this->m);
     unset($this->ops);
     }
 /*
  * Получает значение одной опции
  */
	function getOption($option_name)
	{
		// return $this->m->getCell('optvalue', "optname='$option_name'");
		return $this->ops[$option_name];
	}
 /*
  * Устанавливает значение опции
  */
	function setOption($option_name, $option_value)
	{
            $this->cache->clear('siteoptions');
		// Если записи нет
		if ($this->getOption($option_name)=="")
				{
					$this->m->Insert("optname, optvalue", "'$option_name','$option_value'");
					} else
				{
$this->m->Update("optvalue='$option_value'", "optname='$option_name'");};

		}
/*
 * Обновляет кэш и запись ops
 */                
	function refresh()
	{
		
		 $this->cache->lifetime = 60*60;
	 if ($this->cache->failed('siteoptions')==true)
	 {
	  $this->m->setTable('options');
	 $this->m->Select('optname, optvalue', "1=1");
	 $rows = $this->m->Read();
          foreach ($rows as $index => $d )
	 {
		 $this->ops[$d['optname']] = $d['optvalue'];
	 }
	 $this->cache->save('siteoptions', $this->ops);
	 $this->m->Clear();
	 }
	 else
	 {
	    $this->ops = $this->cache->get_from_cache('siteoptions');
	 };	 
	}
/*
 * Считывает опцию модуля
 */        
        function SetModuleOption($module_name, $option_name, $value)
        {
            $this->setOption('mod_'.$module_name.'_'.$option_name, $value);
        }
/*
 * Получает опцию модуля
 */        
        function GetModuleOption($module_name, $option_name, $value)
        {
            return $this->Option('mod_'.$module_name.'_'.$option_name, $value);
        }
/*
 * Устанавливает опции
 */        
        function SetOptions($options)
        {
            foreach ($options as $opt_name => $opt_value)
            {
                $this->setOption($opt_name, $opt_value);
            };
        }
/*
 * Устанавливает опции модуля
 */        
        function SetModuleOptions($module_name, $options)
        {
            foreach ($options as $opt_name => $opt_value)
            {
                $this->SetModuleOption($module_name, $opt_name, $opt_value);
            };
        }
/*
 * Считывает опции в массив
 */        
        function GetOptions($keys)
        {
            $arr = array();
            foreach ($keys as $key)
            {
                $arr[$key] = $this->getOption($key);
            };
            return $arr;
        }
/*
 * Считывает опции модуля в массив
 */        
        function GetModuleOptions($module_name, $keys)
        {
            $arr = array();
            foreach ($keys as $key)
            {
                $arr[$key] = $this->GetModuleOption($module_name, $key);
            };
            return $arr;            
        }
        
        
    function getAction()
{
	return $this->GetPost('mod_action', '');
}


function action($aTask)
{
	switch ($aTask)
	{
		case 'fillopt' : {$this->FillOpt(); break;};
		case 'addopt' : {$this->Addopt(); break;};
		case 'filledit' : {$this->FillEdit(); break;};
		case 'editopt' : {$this->Editopt(); break;};
		case 'deleteopt' : {$this->Deleteopt(); break;};
		default : {
						$this->ViewOpts(); break;
			   	}

	}
}

function Run()
{
	$this->action($this->getAction());
	return $this->ui;
}
                
}

?>