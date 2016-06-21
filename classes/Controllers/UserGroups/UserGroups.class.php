<?php
if (!defined('APP')) {die('ERROR usergroups.class.php');};
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/ClassFactory/ClassFactory.class.php';

class UserGroups
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
		$this->components['factory'] = new ClassFactory($params);
        $this->components['view'] =  $this->components['factory']->createInstance("TemplateTool", $params, 'Core');
        $this->components['db'] =  $this->components['factory']->createInstance("DatabaseLayer", $params, 'Core');
		$this->components['db']->Plug();
}

function FillRole()
{
	$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/actions/usergroup.tpl');
	$this->components['view']->SetVar("ROLE", "");
	$this->components['view']->SetVar("ROLENAME", "");
	$this->components['view']->SetVar("ACCESS", "5");
	$this->components['view']->SetVar("ACTION", "addrole");
	$this->components['view']->CreateView();
	$this->ui = $this->components['view']->GetView();
}

function FillEdit()
{
	$role = $_POST['role'];
	$this->components['db']->setTable('roles');
	$this->components['db']->Select("role, rolename, access", "role='$role'");
	$rows = $this->components['db']->Read();
  	$data = $rows[0];
	$role = $data['role'];
	$rolename = $data['rolename'];
	$access = $data['access'];


	$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/actions/usergroup.tpl');
	$this->components['view']->SetVar("ROLE", $role);
	$this->components['view']->SetVar("ROLENAME", $rolename);
	$this->components['view']->SetVar("ACCESS", $access);
	$this->components['view']->SetVar("ACTION", "editrole");
	$this->components['view']->CreateView();
	$this->ui = $this->components['view']->GetView();
}

function DeleteRole()
{
	$role = $_POST['role'];
	$this->components['db']->setTable('roles');
	$this->components['db']->Delete("role='$role'");
	$this->ViewRoles();
}

function AddRole()
{
	$role = $_POST['role'];
	$rolename = $_POST['rolename'];
	$access = $_POST['access'];
	$this->components['db']->setTable('roles');
	$this->components['db']->Insert("role, rolename, access", "'$role', '$rolename', $access");
	$this->ViewRoles();
}

function EditRole()
{
	$role = $_POST['role'];
	$rolename = $_POST['rolename'];
	$access = $_POST['access'];
	$this->components['db']->setTable('roles');
	$this->components['db']->Update("role='$role', rolename='$rolename', access='$access'", "role='$old_role'");
	$this->ViewRoles();
}

function ViewRoles()
{


	$h = "";
	$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/items/usergrouplist.tpl');
	$this->components['db']->setTable('roles');
	$this->components['db']->Select("role, rolename, access", "1=1");
	$rows = $this->components['db']->Read();
  	foreach ($rows as $index => $data )
	{
		$this->components['view']->SetVar('ROLE', $data['role']);
		$this->components['view']->SetVar('ROLENAME', $data['rolename']);
		$this->components['view']->SetVar('ACCESS', $data['access']);
		$this->components['view']->CreateView();
		$h .= $this->components['view']->GetView();
	};

	$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/tables/usergroups.table.tpl');
	$this->components['view']->SetVar('ITEMS', $h);
	$this->components['view']->CreateView();
	$this->ui = $this->components['view']->GetView();
}

/////////////////

function getAction()
{
	return $this->GetPost('mod_action', '');
}


function action($aTask)
{
	switch ($aTask)
	{
		case 'fillrole' : {$this->FillRole(); break;};
		case 'addrole' : {$this->AddRole(); break;};
		case 'filledit' : {$this->FillEdit(); break;};
		case 'editrole' : {$this->EditRole(); break;};
		case 'deleterole' : {$this->DeleteRole(); break;};
		default : {
						$this->ViewRoles(); break;
			   	}

	}
}

function Run()
{
	$this->action($this->getAction());
	return $this->ui;
}

}