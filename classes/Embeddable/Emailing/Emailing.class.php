<?php
if (!defined('APP')) {die('ERROR redcivet.class.php');};
// Почтовая форма

require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core/ClassFactory/ClassFactory.class.php';
class Emailing
{
	var $components;
var $mod_path = __DIR__ . '/';
function __construct($params)
{
  $this->components = null;
  $this->components['factory'] = new ClassFactory($params);
  $this->components['db'] =  $this->components['factory']->createInstance("DatabaseLayer", $params, 'Core');
  $this->components['db']->Plug();
  $this->components['view'] = $this->components['factory']->createInstance("TemplateTool", $params);
  $this->components['options'] =  $this->components['factory']->createInstance("CommonSettings", $params, 'Controllers');
  $this->components['captcha'] =  $this->components['factory']->createInstance("CaptchaTool", $params, 'Services');

}
function __destruct()
{
 foreach ($this->components as $key => $value)
	  {
		  unset($this->components[$key]);
	  }
	  unset($this->components);
}
function mail_form()
{
	  $this->components['view']->UseTpl($this->mod_path.'mailform.tpl');
	  $this->components['view']->SetVar('MTHEME_CAPTION', 'Тема письма');
  	  $this->components['view']->SetVar('MBODY_CAPTION', 'Текст письма');
	  $this->components['view']->SetVar('FROM_CAPTION', 'Ваш e-mail');
  	  $this->components['view']->SetVar('DEF_TEXT', '');
   	  $this->components['view']->SetVar('BUTTON', 'Отправить письмо');
   	  $this->components['view']->SetVar('ACTION', 'sendmail');
	  $this->components['view']->SetVar('CAPTCHA', $this->components['captcha']->FormCaptcha());
	  $this->components['view']->CreateView();
	  return $this->components['view']->GetView();
}
function mail_process()
{
if ($this->components['captcha']->check()==false)
{
$this->mail_sended_fail();
}
else
{
    $err = false;
	$to =  $this->components['options']->getOption('EMAIL_ADMIN');
        isset($_POST['mtheme']) ? $subject = $_POST['mtheme'] : $err = true;
        isset($_POST['mailbody']) ? $body = $_POST['mailbody'] : $err = true;
        isset($_POST['mfrom']) ? $from = $_POST['mfrom'] : $err = true;
	
        if ($err == true)
                {
                    $this->mail_no_valid();
                }
                    else            
        {
                    $headers =  "From: $from\r\n";  
                    $headers .= "Reply-To: $from\r\n"; 
                    $headers .= "Content-type: text/html; charset=UTF-8\r\n";                
                    $title = "$subject : письмо с " . $_SERVER['HTTP_HOST']." от $from";        
                    $body = "<html><head><title>$title</title></head><body>$body</body></html>";
                    if (mail($to, $subject, $body, $headers ))
                            {  $this->mail_sended_success();  }                         
                            else 
                                { $this->mail_sended_fail(); };
        }
 };
}

function mail_sended_success()
{
	  $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/readers/actions/sys_message.tpl');
	  $this->components['view']->SetVar('SYS_TITLE', 'Сообщение');
	  $this->components['view']->SetVar('SYS_MESSAGE', 'Письмо отправлено успешно...');
	  $this->components['view']->SetVar('LINK_HREF', "/");
	  $this->components['view']->SetVar('LINK_TITLE', 'продолжить чтение сайта');
	  $this->components['view']->CreateView();
      $this->components['view']->Publish();
}

function mail_sended_fail()
{
	  $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/readers/actions/sys_message.tpl');
	  $this->components['view']->SetVar('SYS_TITLE', 'Сообщение');
	  $this->components['view']->SetVar('SYS_MESSAGE', 'Не удалось отправить письмо...');
	  $this->components['view']->SetVar('LINK_HREF', "/feedback/writemail");
	  $this->components['view']->SetVar('LINK_TITLE', 'попробуйте еще раз');
	  $this->components['view']->CreateView();
      $this->components['view']->Publish();
}

function mail_no_valid()
{
	  $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/readers/actions/sys_message.tpl');
	  $this->components['view']->SetVar('SYS_TITLE', 'Сообщение');
	  $this->components['view']->SetVar('SYS_MESSAGE', 'Вы не заполнили почтовую форму полностью');
	  $this->components['view']->SetVar('LINK_HREF', "/feedback/writemail");
	  $this->components['view']->SetVar('LINK_TITLE', 'попробовать снова');
	  $this->components['view']->CreateView();
      $this->components['view']->Publish();
}




}

?>