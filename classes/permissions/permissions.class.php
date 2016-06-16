<?php
if (!defined('APP')) {die('ERROR');};
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/masterfactory/masterfactory.class.php';

class Permissions
{
var $components; // Компоненты
var $ui;  // Интерфейс


   function GetPost($param, $def)
     {
         $result = '';
         isset($_POST[$param]) ? $result = $_POST[$param] : $result = $def;
         return $result;
     }
       

function __construct($params)
{
	$this->components = null;
		$this->components['factory'] = new MasterFactory($params);
        $this->components['view'] =  $this->components['factory']->createInstance("Lorius", $params);
        $this->components['db'] =  $this->components['factory']->createInstance("SealDB", $params);
		$this->components['db']->Plug();
}

function ViewPermissions() // Просмотр разрешений
{


	$h = "";
	$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/items/permissionlist.tpl');
	$this->components['db']->setTable('permissions');
	$this->components['db']->Select("*", "1=1");
	while ($data = mysql_fetch_array($this->components['db']->sql_result))
	{
		$this->components['view']->SetVar('PMODULE', $data['module']);
		$this->components['view']->SetVar('PACTION', $data['action']);
		$this->components['view']->SetVar('PACCESS', $data['access']);
		$this->components['view']->SetVar('PROLES', $data['roles']);
		$this->components['view']->CreateView();
		$h .= $this->components['view']->GetView();
	};

	$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/tables/permissions.table.tpl');
	$this->components['view']->SetVar('ITEMS', $h);
	$this->components['view']->CreateView();
	$this->ui = $this->components['view']->GetView();
}

function FillNew() // Форма нового разрешения
{
	$module = "";
	$action = "";
	$access = "";


	$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/actions/permission.tpl');
	$this->components['view']->SetVar("PMODULE", $module);
	$this->components['view']->SetVar("PACTION", $rolename);
	$this->components['view']->SetVar("PACCESS", $access);
	$this->components['view']->SetVar("PROLES", 'all');
	$this->components['view']->SetVar("ACTION", "add");
	$this->components['view']->CreateView();
	$this->ui = $this->components['view']->GetView();
}

function FillEdit() // Форма правки
{

	$module = $_POST['pmodule'];
	$action = $_POST['paction'];
	$access = $_POST['paccess'];
	$roles = $_POST['proles'];


	$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/actions/permission.tpl');
	$this->components['view']->SetVar("PMODULE", $module);
	$this->components['view']->SetVar("PACTION", $action);
	$this->components['view']->SetVar("PACCESS", $access);
	$this->components['view']->SetVar("PROLES", $roles);
	$this->components['view']->SetVar("ACTION", "edit");
	$this->components['view']->CreateView();
	$this->ui = $this->components['view']->GetView();
}

function Add() // Добавление в базу
{
    $module = $this->GetPost('module', '');;
    $action = $this->GetPost('action', '');;
    $access = $this->GetPost('access', '');;
	$roles = $this->GetPost('roles', '');;

    $this->components['db']->setTable('permissions');
	$this->components['db']->Insert("module, action, access", "'$module', '$action', $access");
	$this->ui = "Сохранено";
}

function Edit() // Обновление в базе
{
	$module = $this->GetPost('module', '');;
    $action = $this->GetPost('action', '');;
    $access = $this->GetPost('access', '');;
	$roles = $this->GetPost('roles', '');;


    $old_module = $this->GetPost('old_module', '');;
    $old_action = $this->GetPost('old_action', '');;
    $old_access = $this->GetPost('old_access', '');;



    $this->components['db']->setTable('permissions');
	$this->components['db']->Update("module='$module', action='$action', access=$access, roles='$roles'",
			" ( module = '$old_module' ) AND ( action = '$old_action' ) AND ( access = $old_access ) ");
	$this->ui = "Сохранено";
}

function DeletePermission() // Удаляет из базы разрешение
{
	$module = $this->GetPOST('pmodule', '');
    $action = $this->GetPOST('paction', '');
    $access = $this->GetPOST('paccess', '');

    $this->components['db']->setTable('permissions');
	$this->components['db']->Delete(" ( module = '$module' ) AND ( action = '$action' ) AND ( access = $access ) ");


}

/////////////////

function getAction()
{
	return $this->GetPOST('mod_action', '');
}
function action($aTask)
{
	switch ($aTask)
	{
		case 'fillnew' : {$this->FillNew(); break;};
		case 'filledit' : {$this->FillEdit(); break;};
		case 'delete' : {$this->DeletePermission(); break;};
		case 'add' : {$this->Add(); break;};
		case 'edit' : {$this->Edit(); break;};
		default : {
						$this->ViewPermissions(); break;
			   	}

	}
}

function Run()
{
	$this->action($this->getAction());
	return $this->ui;
}

}