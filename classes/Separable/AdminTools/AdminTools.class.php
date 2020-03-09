<?php
// Админка Колибри
if (!defined('APP')) {die('ERROR');};
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core/ClassFactory/ClassFactory.class.php';

class AdminTools
{
var $components; // Фабрика классов

	function __construct($params)
	{
           // echo "Класс админки конструируется<br>";
		$this->components = null;
		$this->components['factory'] = new ClassFactory($params);

     $this->components['view'] =  $this->components['factory']->createInstance("TemplateTool", $params, 'Core');
	 $this->components['security'] = $this->components['factory']->createInstance("UserAuth", $params, 'Services');
	 $this->components['pages'] = $this->components['factory']->createInstance("AdminFuncsPages", $params, 'Controllers');
	 $this->components['files'] = $this->components['factory']->createInstance("AdminFileManagerTool", $params, 'Controllers');
	 $this->components['usergroups'] = $this->components['factory']->createInstance("UserGroups", $params, 'Controllers');
	 $this->components['permissions'] = $this->components['factory']->createInstance("Permissions", $params, 'Controllers');
	 $this->components['sitemap'] = $this->components['factory']->createInstance("SiteMapTool", $params, 'Separable');         
         $this->components['reestr'] = $this->components['factory']->createInstance("CommonSettings", $params, 'Controllers');
         $pr_links = "<a href='/classes/fileutils/antivir.php'>Антивирусный сканер</a> <a href='/classes/fileutils/report.php'>Отчет антивируса</a>";              
	 if (PROTECTION===false)
         {
         $this->components['view']->SetVar('ADM_SPEC_INFO', 'Защита выключена! ' . $pr_links);
         }
         else
         {
         $this->components['view']->SetVar('ADM_SPEC_INFO', 'Защита включена! ' . $pr_links);
         };
         //echo "Конструктор админки выполнен<br/>";
         
         
	}
function __destruct()
{
 foreach ($this->components as $key => $value)
	  {
		  unset($this->components[$key]);
	  }
 unset($this->components);
}
	function welcome()
	{
		 $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/colibri.tpl');
	 $this->components['view']->SetGlobal('admin_menu', $_SERVER['DOCUMENT_ROOT'].'/templates/admin/global/adminmenu.tpl');
	 $this->components['view']->SetVar('TITLE', 'Potto CMS');
	 $this->components['view']->SetVar('HEAD', '');
 	 $this->components['view']->SetVar('BODY', 'Добро пожаловать в админку "Колибри"! Приятной работы!');
	 $this->components['view']->CreateView();
	 $this->components['view']->Publish();
	}

	function actionpage()
	{
     $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/colibri.tpl');
	 $this->components['view']->SetGlobal('admin_menu', $_SERVER['DOCUMENT_ROOT'].'/templates/admin/global/adminmenu.tpl');
	 $this->components['view']->SetVar('TITLE', 'Potto CMS');
	 $h = '<script type="text/javascript" src="/plugins/ckeditor/ckeditor.js"></script>';
	 $this->components['view']->SetVar('HEAD', $h);
 	 $this->components['view']->SetVar('BODY', $this->components['pages']->Run());
	 $this->components['view']->CreateView();
	 $this->components['view']->Publish();
	}
	
	function usergroups()
	{
	  $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/colibri.tpl');
	 $this->components['view']->SetGlobal('admin_menu', $_SERVER['DOCUMENT_ROOT'].'/templates/admin/global/adminmenu.tpl');
	 $this->components['view']->SetVar('HEAD', '');
	 $this->components['view']->SetVar('TITLE', 'Potto CMS');
	 $this->components['view']->SetVar('BODY', $this->components['usergroups']->Run());
	 $this->components['view']->CreateView();
	 $this->components['view']->Publish();
	}
        
        function reestr()
	{
	 $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/colibri.tpl');
	 $this->components['view']->SetGlobal('admin_menu', $_SERVER['DOCUMENT_ROOT'].'/templates/admin/global/adminmenu.tpl');
	 $this->components['view']->SetVar('HEAD', '');
	 $this->components['view']->SetVar('TITLE', 'Potto CMS');
	 $this->components['view']->SetVar('BODY', $this->components['reestr']->Run());
	 $this->components['view']->CreateView();
	 $this->components['view']->Publish();
	}
	
	function permissions()
	{
	  $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/colibri.tpl');
	 $this->components['view']->SetGlobal('admin_menu', $_SERVER['DOCUMENT_ROOT'].'/templates/admin/global/adminmenu.tpl');
	 $this->components['view']->SetVar('HEAD', '');
	 $this->components['view']->SetVar('TITLE', 'Potto CMS');
	 $this->components['view']->SetVar('BODY', $this->components['permissions']->Run());
	 $this->components['view']->CreateView();
	 $this->components['view']->Publish();
	}

	function filemanager()
	{
	  $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/colibri.tpl');
	 $this->components['view']->SetGlobal('admin_menu', $_SERVER['DOCUMENT_ROOT'].'/templates/admin/global/adminmenu.tpl');
	 $this->components['view']->SetVar('HEAD', '');
	 $this->components['view']->SetVar('TITLE', 'Potto CMS');
	 $this->components['view']->SetVar('BODY', $this->components['files']->Run());
	 $this->components['view']->CreateView();
	 $this->components['view']->Publish();
	}

		function actionusers()
	{
     $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/colibri.tpl');
	 $this->components['view']->SetGlobal('admin_menu', $_SERVER['DOCUMENT_ROOT'].'/templates/admin/global/adminmenu.tpl');
	 $this->components['view']->SetVar('TITLE', 'Potto CMS');
     $this->components['view']->SetVar('HEAD', '');
     
 	 $this->components['view']->SetVar('BODY',  $this->components['security']->AdminRun());
	 $this->components['view']->CreateView();
	 $this->components['view']->Publish();
	}
	
	// Очистка кэша
	 function clear_dir ($directory)
  {
  $dir = opendir($directory);
  while(($file = readdir($dir)))
  {
    if ( is_file ($directory."/".$file))
       if ($file!='.htaccess')
		    {
		      unlink ($directory."/".$file);
		    };
  }
  closedir ($dir);
  }

function clear_cache_folders()
{
 $this->clear_dir($_SERVER['DOCUMENT_ROOT'].'/var_cache');
 $this->clear_dir($_SERVER['DOCUMENT_ROOT'].'/cache');
 $this->clear_dir($_SERVER['DOCUMENT_ROOT'].'/webdocs');
 $this->clear_dir($_SERVER['DOCUMENT_ROOT'].'/output_cache');
 $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/colibri.tpl');
	 $this->components['view']->SetGlobal('admin_menu', $_SERVER['DOCUMENT_ROOT'].'/templates/admin/global/adminmenu.tpl');
	 $this->components['view']->SetVar('TITLE', 'Potto CMS');
	 $this->components['view']->SetVar('HEAD', '');
 	 $this->components['view']->SetVar('BODY', 'Кэш страниц и переменных очищен');
	 $this->components['view']->CreateView();
	 $this->components['view']->Publish();
}



    function secaction($aTask)
	{
			switch ($aTask)
		{
		    case 'logout' :  {  $this->components['security']->AdminLogOut();  exit; };
			// Заполнение регистрационной формы
			case 'regform' :
			{
			$this->components['security']->AdminRegForm();
			exit;
			}
			// Регистрация
			case 'register' :
			{
			$this->components['security']->AdminRegistration();
			exit;
			}
			// Заполнение формы для входа
			case 'logform' :
  		    {
                        //echo "Подготавливаем форму входа";
			$this->components['security']->AdminLogForm();
			exit;
			}
			// Авторизация
			case 'login' :
			{
			// Для администратора!!!
			$this->components['security']->AdminLogin();
			exit;
			}
		};

	}

	function Unknown()
    {
            echo "test";
		$this->components['sitemap']->CreateSitemap();
	$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/colibri.tpl');
	 $this->components['view']->SetGlobal('admin_menu', $_SERVER['DOCUMENT_ROOT'].'/templates/admin/global/adminmenu.tpl');
	 $this->components['view']->SetVar('TITLE', 'Potto CMS');
	 $this->components['view']->SetVar('HEAD', '');
 	 $this->components['view']->SetVar('BODY', 'Неизвестное действие!');
	 $this->components['view']->CreateView();
	 $this->components['view']->Publish();
    }
	
	function sitemap()
	{
	$this->components['sitemap']->CreateSitemap();
	$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/colibri.tpl');
	 $this->components['view']->SetGlobal('admin_menu', $_SERVER['DOCUMENT_ROOT'].'/templates/admin/global/adminmenu.tpl');
	 $this->components['view']->SetVar('TITLE', 'Potto CMS');
	 $this->components['view']->SetVar('HEAD', '');
 	 $this->components['view']->SetVar('BODY', 'Карта сайта обновлена...');
	 $this->components['view']->CreateView();
	 $this->components['view']->Publish();
	}
	
	
	
	function action($aTask)
	{
		// Авторизация и регистрация
	    $this->secaction($aTask);
		// Точка безопасности
         if ($this->components['security']->AdminChecking()==false) {
			$this->components['security']->AdminLogForm();
			exit;}

		// Выполнение задачи
		switch ($aTask)
 {
	 // Файловый менеджер
 	 case 'permissions'  : {$this->permissions(); break; }
	 case 'usergroups'  : {$this->usergroups(); break; }
	 case 'filemanager' : {$this->filemanager(); break; };
	 // Задачи по управлению комментариями
	 case 'comments' : {$this->actionpage(); break; };
	 // Задачи по глобальным настройкам сайта
	 case 'configure' : 	 { $this->actionpage(); break; };
         // ВНЕШНИЙ ВИД САЙТА
         // Задачи по управлению таблицами стилей CSS         
	 case 'styles' : 	 { $this->actionpage(); break; };
	 // Задачи по управлению глобальными блоками
	 case 'blocks' : 	 { $this->actionpage(); break; };
	 // Задачи по управлению категориями
	 case 'categories' : { $this->actionpage(); break; };
         case 'reestr' : { $this->reestr(); break; };
     // Задачи по управлению страницами
	 case 'welcome' : 	 { $this->welcome(); break; };
	 case 'pages'   :     { $this->actionpage(); break; };
// 	 Задачи по управлению пользователями
  	 case 'users' : { $this->actionusers(); break; };
   	 case 'logs' : { $this->actionusers(); break; };
	 case 'clearcachefolders' : {$this->clear_cache_folders(); break;};
	 case 'sitemap' : { $this->sitemap(); break; };
	 default : {echo "Неизвестное действие!"; $this->Unknown();};
         
         
 };
	}
	function getAction()
	{
                //echo "Узнаем таск";
                $aTask = $_GET['do'];
		return $aTask;
	}
	function run()
	{
         //echo "Запущен контроллер!";
	 $this->action($this->getAction());
	}
}



?>