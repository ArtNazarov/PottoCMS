<?php
if (!defined('APP')) {die('ERROR');};
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/masterfactory/masterfactory.class.php';

class Reestr
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

function FillOpt()
{
	$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/actions/opt.tpl');
	$this->components['view']->SetVar("optname", "");
	$this->components['view']->SetVar("optvalue", "");
	$this->components['view']->SetVar("optnote", "");
	$this->components['view']->SetVar("ACTION", "addopt");
	$this->components['view']->CreateView();
	$this->ui = $this->components['view']->GetView();
}

function FillEdit()
{
	$optname = $_POST['optname'];
	$this->components['db']->setTable('options');
	$this->components['db']->Select("optname, optvalue, optnote", "optname='$optname'");
	$data = mysql_fetch_array($this->components['db']->sql_result);
	$optname = $data['optname'];
	$optvalue = $data['optvalue'];
	$optnote = $data['optnote'];


	$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/actions/opt.tpl');
	$this->components['view']->SetVar("optname", $optname);
	$this->components['view']->SetVar("optvalue", $optvalue);
	$this->components['view']->SetVar("optnote", $optnote);
	$this->components['view']->SetVar("ACTION", "editopt");
	$this->components['view']->CreateView();
	$this->ui = $this->components['view']->GetView();
}

function Deleteopt()
{
	$optname = $_POST['optname'];
	$this->components['db']->setTable('options');
	$this->components['db']->Delete("optname='$optname'");
	$this->ViewOpts();
}

function Addopt()
{
	$optname = $_POST['optname'];
	$optvalue = $_POST['optvalue'];
	$optnote = $_POST['optnote'];
	$this->components['db']->setTable('options');
	$this->components['db']->Insert("optname, optvalue, optnote", "'$optname', '$optvalue', '$optnote'");
	$this->ViewOpts();
}

function Editopt()
{
    	$old_optname = $_POST['old_optname'];
	$optname = $_POST['optname'];
	$optvalue = $_POST['optvalue'];
	$optnote = $_POST['optnote'];
	$this->components['db']->setTable('options');
	$this->components['db']->Update("optname='$optname', optvalue='$optvalue', optnote='$optnote'", "optname='$old_optname'");
	$this->ViewOpts();
}

function ViewOpts()
{


	$h = "";
	$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/items/optlist.tpl');
	$this->components['db']->setTable('options');
	$this->components['db']->Select("optname, optvalue, optnote", "1=1 ORDER BY optname ");
	while ($data = mysql_fetch_array($this->components['db']->sql_result))
	{
		$this->components['view']->SetVar('optname', $data['optname']);
		$this->components['view']->SetVar('optvalue', $data['optvalue']);
		$this->components['view']->SetVar('optnote', $data['optnote']);
		$this->components['view']->CreateView();
		$h .= $this->components['view']->GetView();
	};

	$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/tables/opt.table.tpl');
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