<?php // Личные сообщения
if (!defined('APP')) {die('ERROR mouse.class.php');};
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core/ClassFactory/ClassFactory.class.php';

class PrivateMessages
{
	var $components;
	var $mod_path = __DIR__ . '/';
	function __construct(array $params)
	{
		$this->components['factory'] = new ClassFactory($params);
		$this->components['view'] = $this->components['factory']->createInstance("TemplateTool", $params, 'Core');
		$this->components['db'] = $this->components['factory']->createInstance("DatabaseLayer", $params, 'Core');
		$this->components['db']->Plug();
		$this->components['log'] = $this->components['factory']->createInstance("Log", $params, 'Core');
	}
	function __destruct()
	{
		foreach ($this->components as $key => $value)
	  {
		  unset($this->components[$key]);
	  }
	  unset($this->components);
	}



	function form_write_pm() // Форма для ввода личного сообщения
	{
    $this->components['db']->setTable('users');
	$aKey = $_SESSION['ukey'];
	$this->components['db']->Select("user", "ukey='$aKey'");
	$data = $this->components['db']->Read()[0];
	$this->components['view']->UseTpl($this->mod_path.'pm_form.tpl');
	$this->components['view']->SetVar('USERNAME', $data['user']);
	$this->components['view']->CreateView();
	return $this->components['view']->GetView();

	}
	function send_pm()
	{
		$aFrom = $_POST['from'];
		$aTo = $_POST['to'];
		$aMessage = strip_tags($_POST['message']);
		$this->components['db']->setTable('pm');
	do
	{
		$id_pm  = rand(1, 99999);
	$this->components['db']->Select('id_pm', "id_pm=$id_pm");
	$data = $this->components['db']->Read();
	} while ($this->components['db']->NRows()>0);

	$this->components['db']->Insert("id_pm, usr1, usr2, message, readed",
                      "$id_pm, '$aFrom', '$aTo', '$aMessage', 'no'");
	$this->components['log']->WriteLog('system', "ЛИЧНОЕ СООБЩЕНИЕ:$id_pm, '$aFrom', '$aTo', '$aMessage', 'no'</BR>");

    $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/readers/actions/sys_message.tpl');
	  $this->components['view']->SetVar('SYS_TITLE', 'Сообщение');
	  $this->components['view']->SetVar('SYS_MESSAGE', 'Письмо в личку отправлено...');
	  $this->components['view']->SetVar('LINK_HREF', "/");
	  $this->components['view']->SetVar('LINK_TITLE', 'вернуться к странице...');
	  $this->components['view']->CreateView();
	  $this->components['view']->Publish();
	} 
	function inbox_pm() // Входящие сообщения, списком
	{
	$h = '';
	$this->components['db']->setTable('users');
	$aKey = $_SESSION['ukey'];
	$this->components['db']->Select('user', "ukey='$aKey'");
	$rows = $this->components['db']->Read();
         foreach ($rows as $index => $data )
	{
	$aUser = $data['user'];
	}
	$this->components['db']->Clear();
    $this->components['db']->setTable('pm');
    $this->components['db']->Select('id_pm, usr1, usr2, message', "usr2='$aUser'");

  	    $this->components['view']->UseTpl($this->mod_path.'pm_inbox.tpl');
	 $rows = $this->components['db']->Read();
         foreach ($rows as $index => $data )
        {
			$this->components['view']->SetVar('READED', $this->components['view']->Choice(
			    ($data['readed']=="no"), 'Новое', 'Прочитанное'
			    ));
            $this->components['view']->SetVar('ID_PM', $data['id_pm']);
			$this->components['view']->SetVar('FROM', $data['usr2']);
			$this->components['view']->SetVar('TEXT', substr(trim($data['message']), 0, 30));
			$this->components['view']->CreateView();
			$h .= $this->components['view']->GetView();
		};

		$this->components['db']->Done();
		return $h;
	}
	function outbox_pm() // Исходящие сообщения, списком
	{
			$this->components['db']->setTable('users');
		$aKey = $_SESSION['ukey'];
	$this->components['db']->Select('user', "ukey='$aKey'");
	$rows = $this->components['db']->Read();
        $data = $rows[0];
	$aUser = $data['user'];


		$h = '';
		$this->components['db']->setTable('pm');
		$this->components['view']->UseTpl($this->mod_path.'pm_outbox.tpl');
	     $this->components['db']->Select('id_pm, usr1, usr2, message', "usr1='$aUser'");

                $rows = $this->components['db']->Read();
  	        foreach ($rows as $index => $data )

        {
			@ $this->components['view']->SetVar('READED', $this->components['view']->Choice(
			    ($data['readed']=="no"), 'Не прочитано получателем', 'Прочитанное получателем'
			    ));
            $this->components['view']->SetVar('ID_PM', $data['id_pm']);
			$this->components['view']->SetVar('TO', $data['usr2']);
			$this->components['view']->SetVar('TEXT', substr(trim($data['message']), 0, 30));
			$this->components['view']->CreateView();
			$h .= $this->components['view']->GetView();
		};
		$this->components['db']->Clear();

		$this->components['db']->Done();
		return $h;
	}
	function open_pm($aID) // Читает сообщение
	{

		$h = '';
	$this->components['db']->setTable('users');
	$aKey = $_SESSION['ukey'];
	$this->components['db']->Select('user', "ukey='$aKey'");
	$rows = $this->components['db']->Read();
  	foreach ($rows as $index => $data )
	{
	$aUser = $data['user'];
	}
	$this->components['db']->Clear();

	$this->components['db']->setTable('pm');
		$h = '';
		$this->components['db']->Select('id_pm, usr1, usr2, message', "id_pm=$aID");
  	    $this->components['view']->UseTpl($this->mod_path.'pm_open.tpl');
		 $rows = $this->components['db']->Read();
  	        foreach ($rows as $index => $data )
        {
			$this->components['view']->SetVar('FROM', $data['usr1']);
			$this->components['view']->SetVar('TO', $data['usr2']);
			$this->components['view']->SetVar('TEXT', $data['message']);
			$this->components['view']->CreateView();
			$h .= $this->components['view']->GetView();
		};
		$this->components['db']->Clear();
		if ($aUser == $data['usr2']) {
		$this->components['db']->Update("readed='yes'", "id_pm=$aID"); };
		$this->components['db']->Done();
		return $h;
	}
	

	
	
}

?>