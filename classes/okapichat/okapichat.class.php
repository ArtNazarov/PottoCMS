<?php
if (!defined('APP')) {die('ERROR');};
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/masterfactory/masterfactory.class.php';

class OkapiChat
{
  var $components; // Компоненты
  var $messages; // Сообщения
  var $count; // Число сообщений
  var $mod_path = '/classes/okapichat/';
  function __construct($params)
  {
  	  $this->components = null;
	  $this->components['factory'] = new MasterFactory($params);
	  $this->components['view'] = $this->components['factory']->createInstance("Lorius", $params);
	  $this->components['usr'] = $this->components['factory']->createInstance("Meerkat", $params);
	  $this->messages = new ArrayObject;
	  $this->count = 0;
  }
  function __destruct()
  {
	  foreach ($this->components as $key => $value)
	  {
		  unset($this->components[$key]);
	  }
	  unset($this->components);
  }
  function getChatLog()
  {
	  return $_SERVER['DOCUMENT_ROOT']."/chat.log";
  }

  function getchatpage()
  {
  return $this->components['view']->PasteFile($_SERVER['DOCUMENT_ROOT'].$this->mod_path.'chat.tpl');
  }
  function getview()
  {
	  $this->loadmessages();
	  $v = $this->components['usr']->GetUsersOnline();
	  $v .=  "<div class='chat'>";
	  if ($this->count == 0)
	     { $v=" Пока сообщений нет..."; }
		 			else
		 {
				  for ($i=1; $i<$this->count; $i++)
					  {
						  $username = $this->messages[$i]["username"];
						  $date =  $this->messages[$i]["date"];
						  $message = $this->messages[$i]["message"];
					  $v .= "<p class='chatline'>$username < $date > : <br/> $message</p>";
					  };
		  };
	  return $v;
	  }
  function loadmessages()
  {
	  $fh = @fopen($this->getChatLog(), "r+");
      $fcontent = @fread($fh, filesize($this->getChatLog()));
 	  fclose($fh);
		 $temparr = explode("%%%", $fcontent);

	  $this->count = 0;
	  foreach ($temparr as $key => $value)
	  {
		  $temprec = explode("%%", $value);
		   $this->count++;
		  $i = $this->count;
		  $this->messages[$i] = new ArrayObject;
		  $this->messages[$i]["date"] = $temprec[0]; // Дата
		 @  $this->messages[$i]["username"] = @ $temprec[1]; // Имя пользователя
		 @ $this->messages[$i]["message"] = @ $temprec[2]; // Сообщение
	  };


   }
  function cleanmessages()
  {
	  $fh = fopen($this->getChatLog(), "r+");
      $fcontent = @fread($fh, filesize());
 	  @fclose($fh);
     	if (strpos($fc, ';')>0)
	  {
	  $temparr = explode("%%%", $fcontent);
	  $num = count($temparr);
	  if ($num>20) {$start_num = $num-20; $end_num = $num;} else {$start_num = 1; $end_num = $num;};
	  $fh = fopen($this->getChatLog(), "r+");
	  $fcontent = '';
	  for ($i=$start_num; $i<=$end_num; $i++)
	 		 {
				 $fcontent .= $temparr[$i].'%%%';
			  };
       fwrite($fh, $fcontent);
	   fclose($fh);
	  };
  }

  function addmessage($date, $username, $message)
  {
	  $this->cleanmessages();
	  $fh = fopen($this->getChatLog(), "r+");
      $fcontent = @fread($fh, filesize($this->getChatLog()));
 	  @fclose($fh);
	  $fcontent = "$date%%$username%%$message%%%".$fcontent;
	  $fh = fopen($this->getChatLog(), "w");
      fwrite($fh, $fcontent);
 	  fclose($fh);
}
}
?>