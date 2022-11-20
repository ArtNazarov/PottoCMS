<?php
if (!defined('APP')) {die('ERROR meerkat.class.php');};
 include_once $_SERVER['DOCUMENT_ROOT'].'/config/sysconst.php';
 require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core/ClassFactory/ClassFactory.class.php';
 require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core/CacheLayer/CacheLayer.class.php';
 // проверка типов
 require_once $_SERVER['DOCUMENT_ROOT'].'/collections/typecheckers.collection.php';                               
// Авторизация пользователя
 
 
 
 
class UserAuth
{
        var $visualized; // Для внешних шаблонов
        var $components;

        public function __construct(array &$params)
        {
        $this->components = [];
        $this->components['factory'] = new ClassFactory($params); // Фабрика классов
 // Настройки базы данныx  
if (is_null($params)) {echo "Параметры UserAuth не заданы";exit();}; 
//echo var_dump($params); 
if (check_class($params, 'db', 'DataBaseLayer')) // проинициализирован
     { // Не создаем новый объект базы данных, используем переданный
     $this->components['db'] = &$params['db'];
     
                                
     }
     else
     { // Конструируем новый объект базы данных
         
                                
     
 	 $this->components['db'] = $this->components['factory']->createInstance("DatabaseLayer", $params, 'Core');
 	 };
         $this->components['db']->Plug();
         $this->components['db']->setTable('users');
         $this->components['view'] = $this->components['factory']->createInstance("TemplateTool", $params, 'Core');
         $this->components['log'] = $this->components['factory']->createInstance("Log", $params, 'Core');
         $this->components['formitems'] = $this->components['factory']->createInstance("FormItems", $params, 'Core');
         $this->components['captcha'] = $this->components['factory']->createInstance("CaptchaTool", $params, 'Services');
         $this->components['var_cache'] = $this->components['factory']->createInstance("CacheLayer", $params, 'Core');
	 $this->components['var_cache']->lifetime = 600;
        }

        function __destruct()
        {
  foreach ($this->components as $key => $value)
          {
                  unset($this->components[$key]);
          }
          unset($this->components);
        }
		
	function GetUsernameFS()
{
        $this->components['db']->setTable('users');
        if ( isset($_SESSION['ukey'])  )
		  { $ukey = $_SESSION['ukey']; }
		else {$ukey = ""; };
        $this->components['db']->Select('user', "ukey='$ukey'");
        $data = $this->components['db']->Read()[0];
        $username = $data['user'];
        return $username;
}	

	function GetRoleFS()
{
        $this->components['db']->setTable('users');
        if ( isset($_SESSION['ukey'])  )
		  { $ukey = $_SESSION['ukey']; }
		else {$ukey = ""; };
        $this->components['db']->Select('role', "ukey='$ukey'");
        $data = @$this->components['db']->Read()[0];
        if (!is_null($data)){
        $role = $data['role'];
        }
        else
        {
            $role='guest';
        }
        return $role;
}	

	
   function GetPost($param, $def)
     {
         $result = '';
         isset($_POST[$param]) ? $result = $_POST[$param] : $result = $def;
         return $result;
     }
        
        
		
function GetUserOption($username, $optname) // Сохраняет пользовательскую настройку
{
$this->components['db']->setTable('useroptions');
$this->components['db']->Select('optvalue', " (username='$username' AND optname='$optname' )");
$data = $this->components['db']->Read()[0];
return $data['optvalue'];
}

function UserCacheName($username)
{
return "useroptions".md5($username);
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
}

		
function GetUserOptions($username) // Получает настройки
{
$this->components['db']->setTable('useroptions');
$this->components['db']->Select('optname, optvalue', "username='$username'");
$cache = $this->UserCacheName($username);
// Не существует?
if ($this->components['var_cache']->failed($cache)==true)
{
$rows = $this->components['db']->Read();    
foreach ($rows as $i => $data)
	{
	   $optname = $data['optname'];
	   $optvalue = $data['optvalue'];
	   $arr[$optname] = $optvalue;
	};	
	$this->components['var_cache']->clear($cache); // Удаляем кэш
	$this->components['var_cache']->save($cache, $arr); // Записываем новый
}
else
{
   // Из кэша
   $arr = $this->components['var_cache']->get_from_cache($cache);
};

return $arr;	
}

function SetUserOption($username, $optname, $optvalue) // Получает пользовательскую настройку
{
$this->components['var_cache']->clear($cache); // Удаляем кэш    
$this->components['db']->setTable('useroptions');
$this->components['db']->Select('*', " ( username='$username') AND ( optname='$optname' ) ");
if ($this->components['db']->NRows()>0)
{
$this->components['db']->Update("optname='$optname', optvalue='$optvalue'",
			" ( username='$username' ) AND ( optname='$optname' ) ");
}
else
{
$this->components['db']->setTable('useroptions');
$this->components['db']->Insert('username, optname, optvalue', "'$username','$optname', '$optvalue'");
};
}

function SetUserOptions($username, $options) // Назначает настройки
{
foreach ($options as $optname => $optvalue)
		{
			$this->SetUserOption($username, $optname, $optvalue);
		};
  $username = $this->GetUsernameFromSession();
  $cache = $this->UserCacheName($username);
  // Пишем обновления (всю структуру)  
  $this->components['var_cache']->clear($cache); // Удаляем кэш
  $this->components['var_cache']->save($cache, $options); // Записываем новый
  
  $this->clear_cache_folders();
}


function w_UserOptions() // Профиль пользователя
{
 $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/misc/useroptions.tpl'); 
 
 $user_options_keys = array('email', 'deviz', 'adres1', 'adres2',
   'country', 'region', 'city', 'sex', 'birthday', 'webpage',
   'phone', 'aboutinfo');   
  
   
 $username = $this->GetUsernameFromSession();
 $options = $this->GetUserOptions($username);
 
 foreach ($user_options_keys as $useropt)
  {
   if ($options[$useropt] == ('~'.$useropt.'~') )
		{ $options[$useropt] = ''; };
  };  
 
 $this->components['view']->SetVars($options);
 $this->components['view']->CreateView();
 return $this->components['view']->GetView();
}

function db_SaveUserOptions()
{
  $user_options_keys = array('email', 'deviz', 'adres1', 'adres2',
   'country', 'region', 'city', 'sex', 'birthday', 'webpage',
   'phone', 'aboutinfo');

   $options = array();
   
  foreach ($user_options_keys as $useropt)
  {
   $options[$useropt] = $_POST[$useropt];
  };  
  
  $username = $this->GetUsernameFromSession();
  $this->SetUserOptions($username, $options);
  
}
		




// Журнал авторизации		
function AuthLog($message)		
{
$alog = $_SERVER['DOCUMENT_ROOT'].'/logs/auth.log';
		if (file_exists($alog))
		{
		$fh=fopen($alog, 'a');
		} else {$fh=fopen($alog, 'w');};
fwrite($fh,	$message);
fclose($fh);
}
// Имя по ключу сессии
function GetUsernameFromSession()
{
 
 
        $ukey = $this->GetKeyFromSession();
		$key = 'guest';
		if ( isset($_SESSION['ukey']) == true) 
		{
		$key = $_SESSION['ukey'];		
		};
		$cache = "usernames".md5($key);
		if ($this->components['var_cache']->failed($cache)==true)
		{
		$this->components['db']->setTable('users');
                $this->components['db']->Select('user', "ukey='$ukey'");
                $data = $this->components['db']->Read()[0];
                $username = $data['user'];        
		$this->components['var_cache']->save($cache, $username);
		}
		else
		{
		$username = $this->components['var_cache']->get_from_cache($cache);
		}
        return $username;
}

        // ====================================================
        // Cообщения и формы


// ФРОНТЭНД ПОЛЬЗОВАТЕЛЯ
// Профиль пользователя (как виджет)
    function UserProfile()
        {
                $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/readers/global/userprofile.tpl');
                $this->components['view']->SetVar('USERNAME', $this->GetUsernameFromSession());				
				$this->components['view']->SetVar('USERROLE', $this->GetRoleName(
																$this->GetRole($this->GetUsernameFromSession()
																							)
																)
												);
                $this->components['view']->CreateView();
                return $this->components['view']->GetView();
        }

function GetRoleName($aRole)
{
$this->components['db']->setTable('roles');
$this->components['db']->Select('rolename', "role='$aRole'");
$data = $this->components['db']->Read()[0];
return $data['rolename'];
}
		
// Оповещение о успешной регистрации
        function UserRegisterOkMsg()
{
        $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/readers/actions/sys_message.tpl');
    $this->components['view']->SetVar('SYS_TITLE', 'Сообщение');
        $this->components['view']->SetVar('SYS_MESSAGE', 'Вы зарегистрировались!');
        $this->components['view']->SetVar('LINK_HREF', "/");
        $this->components['view']->SetVar('LINK_TITLE', 'продолжить работу...');
        $this->components['view']->CreateView();
        $this->components['view']->Publish();
}
// Оповещение о неудачной регистрации
function UserRegisterFailMsg()
{
        $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/readers/actions/sys_message.tpl');
    $this->components['view']->SetVar('SYS_TITLE', 'Сообщение');
        $this->components['view']->SetVar('SYS_MESSAGE', 'Не получилось зарегистрироваться :(');
        $this->components['view']->SetVar('LINK_HREF', "/");
        $this->components['view']->SetVar('LINK_TITLE', 'продолжить работу...');
        $this->components['view']->CreateView();
        $this->components['view']->Publish();
}

function UserLogForm() // Форма входа для пользователей
        {
         $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/readers/actions/logform.tpl');
         $this->components['view']->SetVar('SYS_MESSAGE', 'Форма входа');
         $this->components['view']->SetVar('BUTTON', 'Войти на сайт');
         $this->components['view']->SetVar('ACTION', 'login');
         $this->components['view']->SetVar('ACTION2', 'regform');
         $this->components['view']->SetVar('TLINK2', 'Зарегистрироваться');
		 
         $this->components['view']->CreateView();
     return $this->components['view']->GetView();
        }

function UserRegForm() //Форма регистрации для пользователей
        {
         $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/readers/actions/register.tpl');
         $this->components['view']->SetVar('BUTTON', 'Зарегистрироваться');
         $this->components['view']->SetVar('ACTION', 'register');
         $this->components['view']->SetVar('ACTION2', 'logform');
         $this->components['view']->SetVar('TLINK2', 'Войти');
		  
         $this->components['view']->CreateView();
         return $this->components['view']->GetView();
        }

function UserLoginOkMsg() // Сообщение об успешной авторизации
{
$this->AuthLog('[OK] LOGIN DATETIME = '.date("d-M-y H:m:s")."<br/>");
        $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/readers/actions/sys_message.tpl');
    $this->components['view']->SetVar('SYS_TITLE', 'Сообщение');
        $this->components['view']->SetVar('SYS_MESSAGE', 'Авторизация успешна!');
        $this->components['view']->SetVar('LINK_HREF', "/");
        $this->components['view']->SetVar('LINK_TITLE', 'продолжить работу...');
        $this->components['view']->CreateView();
        $this->components['view']->Publish();
}

function UserLoginFailMsg() // Сообщение в неудачной авторизации
{
$this->AuthLog('[FAIL] LOGIN DATETIME = '.date("d-M-y H:m:s")."<br/>");
        $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/readers/actions/sys_message.tpl');
    $this->components['view']->SetVar('SYS_TITLE', 'Сообщение');
        $this->components['view']->SetVar('SYS_MESSAGE', 'Авторизация не удалась :(');
        $this->components['view']->SetVar('LINK_HREF', "/");
        $this->components['view']->SetVar('LINK_TITLE', 'продолжить работу...');
    $this->components['view']->CreateView();
    $this->components['view']->Publish();
}
// Оповещение о выходе 
        function UserLogOutMsg()
        {
		$this->AuthLog('[OK] LOGOUT DATETIME = '.date("d-M-y H:m:s")."<br/>");
        $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/readers/actions/sys_message.tpl');
    $this->components['view']->SetVar('SYS_TITLE', 'Сообщение');
        $this->components['view']->SetVar('SYS_MESSAGE', 'Вы успешно вышли');
        $this->components['view']->SetVar('LINK_HREF', "/");
        $this->components['view']->SetVar('LINK_TITLE', 'продолжить чтение с правами гостя');
    $this->components['view']->CreateView();
    $this->components['view']->Publish();
        }
// Оповещение о успешной регистрации через admin
        function AdminRegisterOkMsg()
{
         $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/actions/sysmessage.tpl');
         $this->components['view']->SetVar('PAGE', 'Сообщение');
         $this->components['view']->SetVar('SYS_MESSAGE', 'Регистрация успешна');
         $this->components['view']->SetVar('ACTION', 'logform');
         $this->components['view']->SetVar('TLINK', 'Зайдите на сайт');		 
         $this->components['view']->CreateView();
         $this->components['view']->Publish();
}
// Оповещение о неудачной регистрации через admin
function AdminRegisterFailMsg()
{
         $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/actions/sysmessage.tpl');
         $this->components['view']->SetVar('PAGE', 'Сообщение');
         $this->components['view']->SetVar('SYS_MESSAGE', 'Регистрация не удалась :( <br/> Скорее всего пользователь с таким ником существует!');
         $this->components['view']->SetVar('ACTION', 'regform');
         $this->components['view']->SetVar('TLINK', 'Попробуйте зарегистрироваться снова');
         $this->components['view']->CreateView();
         $this->components['view']->Publish();
}

function AdminLoginOkMsg() // Сообщение в админке об успешной авторизации
{
         $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/actions/sysmessage.tpl');
         $this->components['view']->SetVar('PAGE', 'Сообщение');
         $this->components['view']->SetVar('SYS_MESSAGE', 'Авторизация в качестве администратора успешна!');
         $this->components['view']->SetVar('ACTION', 'welcome');
         $this->components['view']->SetVar('TLINK', 'Начать работу с админкой');
         $this->components['view']->CreateView();
         $this->components['view']->Publish();
}

function AdminLoginFailMsg() // Сообщение в админке о неудачной авторизации
{
         $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/actions/sysmessage.tpl');
         $this->components['view']->SetVar('PAGE', 'Сообщение');
         $this->components['view']->SetVar('SYS_MESSAGE', 'Авторизация в качестве администратора не удалась или не была выполнена');
         $this->components['view']->SetVar('ACTION', 'logform');
         $this->components['view']->SetVar('TLINK', 'Попробуйте войти снова');
         $this->components['view']->CreateView();
         $this->components['view']->Publish();
}

        function AdminLogForm() // Форма входа для администратора
        {
        $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/actions/auth.tpl');
         $this->components['view']->SetVar('PAGE', 'Авторизация');
         $this->components['view']->SetVar('SYS_MESSAGE', 'Представьтесь!');
         $this->components['view']->SetVar('BUTTON', 'Войти на сайт');
         $this->components['view']->SetVar('ACTION', 'login');
         $this->components['view']->SetVar('ACTION2', 'regform');
         $this->components['view']->SetVar('TLINK2', 'Зарегистрироваться');
         // $this->components['view']->SetVar('CAPTCHA', $this->components['captcha']->FormCaptcha());
         $this->components['view']->SetVar('CAPTCHA', '');
         $this->components['view']->CreateView();
         $this->components['view']->Publish();
        }

        function AdminRegForm() // У администратора форма регистрации
        {
         $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/actions/auth.tpl');
         $this->components['view']->SetVar('PAGE', 'Регистрация');
         $this->components['view']->SetVar('SYS_MESSAGE', 'Введите данные для регистрации');
         $this->components['view']->SetVar('BUTTON', 'Зарегистрироваться');
         $this->components['view']->SetVar('ACTION', 'register');
         $this->components['view']->SetVar('ACTION2', 'logform');
         $this->components['view']->SetVar('TLINK2', 'Войти');
         //$this->components['view']->SetVar('CAPTCHA', $this->components['captcha']->FormCaptcha());
         $this->components['view']->SetVar('CAPTCHA','');
         $this->components['view']->CreateView();
         $this->components['view']->Publish();
        }
// Оповещение о выходе из админки
        function AdminLogOutMsg()
        {
        $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/actions/sysmessage.tpl');
         $this->components['view']->SetVar('PAGE', 'Сообщение');
         $this->components['view']->SetVar('SYS_MESSAGE', 'Вы успешно вышли');
         $this->components['view']->SetVar('ACTION', 'logform');
         $this->components['view']->SetVar('TLINK', 'Зайдем снова? :)');
         $this->components['view']->CreateView();
         $this->components['view']->Publish();
        }

        // ====================================================
        // Запросы к базе и процедуры
        // ====================================================


        function AdminAuthorization() // Авторизация администратора
{
 $this->Authorization(); 
}

function AdminChecking() // Проверка прав администратора
{
  $ok_key = $this->CheckKey();
  $ok_role = $this->AdminRole($this->GetKeyFromSession());
  return ($ok_key==true) and ($ok_role==true);
}

function UserChecking() // Проверка прав администратора
{
  $ok_key = $this->CheckKey();
  $ok_role = $this->UserRole($this->GetKeyFromSession());
 // поставить true
  return ($ok_key==true) and ($ok_role==true);
}

function AdminLogin() // Процедура входа в админку
{
$this->AdminAuthorization();
$ok_key = $this->CheckKey();
//$ok_role = $this->AdminRole($this->GetKeyFromSession());
// выключили капчу or ($this->components['captcha']->check()==false)
// or ($ok_role==false)
if (($ok_key==false)   )
 {$this->AdminLoginFailMsg();

  } else { $this->AdminLoginOkMsg(); };
}

        public function Authorization() // Авторизация пользователей
        {
		
        isset($_POST['user']) ? $aUser = $_POST['user'] : $aUser = 'GUESTDEMO';
        isset($_POST['password']) ? $aPassword = $_POST['password'] : $aPassword = 'GUESTDEMO';
        $_SESSION['ukey'] = md5($aUser.$aPassword.PSALT);		
		$this->AuthLog('[AUTH] DATETIME = '.date("d-M-y H:m:s")."; USERNAME = $aUser.<br/>");		
        }
        public function UserExists($aUser) // Проверка существования пользователя
        {
        $this->components['db']->setTable('users');
        $this->components['db']->Select('user', "user='$aUser'");
	$data =	$this->components['db']->Read()[0];
	$z = $data['user'];
        return ($z == $aUser);
        }

        function CheckKey() // Сверка ключей
        {
         $ukey = $this->GetKeyFromSession();
         //echo "Код ". $ukey . "<br/>";
         //echo "Надо ". $this->SearchKey($ukey) . "<br/>";
     return (($this->SearchKey($ukey)==$ukey) and ($ukey!=''));
        }

        public function GetRole($aUser) // Проверка роли
{
       $this->components['db']->setTable('users');
       $this->components['db']->Select('role', "user='$aUser'");
       $data = $this->components['db']->Read()[0];
       return $data['role']; 
}

public function SearchKey($aKey) // Поиск ключа в базе
{
    $this->components['db']->setTable('users');
    // TODO Проблема с getCell
    $this->components['db']->Select('ukey', "ukey='$aKey'");
    $data = $this->components['db']->Read()[0];
    //echo $data['ukey'];
    return $data['ukey'];
}

public function AdminRole($aKey) // Права администратора?
{
        $this->components['db']->setTable('users');
        $this->components['db']->Select('role', "ukey='$aKey'");
        $data = $this->components['db']->Read()[0];
        return ('admin'==$data['role']);
}

public function UserRole($aKey) // Права администратора?
{
		$this->components['db']->setTable('users');
                $this->components['db']->Select('role', "ukey='$aKey'");
        $Role = $this->components['db']->Read()[0]['role'];
        return (('user'==$Role) or ('admin'==$Role) or ('seller'==$Role)) ;
}

public function GetAction() // Узнать действие
{
        return $this->GetPost('mod_action', '');
}

public function GetKeyFromSession() // Ключ из сессионной переменной
{
  if (isset($_SESSION['ukey']))
  {
                 $ukey = $_SESSION['ukey'];
   } else {$ukey = ""; };
				 return $ukey;				 
}

function MarkOnline() // Отмечает, что пользователь онлайн
{
$this->components['db']->setTable('users');
$ukey = $this->GetKeyFromSession();
$this->components['db']->Update('online=1', "ukey='$ukey'");
}

function MarkOffline() // Отмечает, что пользователь оффлайн
{
$this->components['db']->setTable('users');
$ukey = $this->GetKeyFromSession();
$this->components['db']->Update('online=0', "ukey='$ukey'");
}

function GetUsersOnline()
{
$this->components['db']->setTable('users');
$this->components['db']->Select('user, role', 'online=1');
$userlist = 'пользователи: ';
$count = 0;
$rows = $this->components['db']->Read();
foreach ($rows as $i => $data)
{
$role = $data['role'];
$userlist .= "<span class='usr$role'>".$data['user'].'</span> ; ';
$count = $count + 1;
};
if ($count==0) {$userlist = '';}
return "<div class='w_online'>Авторизованных сейчас $count<br/>".$userlist.'</div>';
}


function UserLogin()
{
$this->Authorization(); // Обычная авторизация
$ok_key = $this->CheckKey(); // Создан ли ключ
if ($ok_key==false)
 {  $this->UserLoginFailMsg();  } else { $this->UserLoginOkMsg(); $this->MarkOnline(); };
}



 public function MakeKey($aUser, $aPassword) // Формирование ключа
        {
$s = '';
//$this->components['log']->WriteLog('test', PSALT);
$s .= md5($aUser.$aPassword.PSALT);
return $s;
        }

   public function AdminRegistration() // Регистрация
        {
       //$this->components['captcha']->check()==false
		if (false)
		{$this->AdminRegisterFailMsg();}
		else
		{
                // Имя пользователя
                $aUser = $_POST['user'];
                // Пароль
                $aPassword = $_POST['password'];
                // Если пользователя нет в базе, то выводим сообщение об ошибке
                if ($this->UserExists("$aUser") == true) {$this->RegisterFailMsg();} else
                {
                // в обратном случае регистрация
                // Создаем ключ на основе пары имя пользователя - пароль
                 $aKey = $this->MakeKey($aUser, $aPassword);
           // Регистрируем пользователя
         $this->RegisterUser($aUser, $aKey, 'user');
                // Проверяем, была ли успешна регистрация
                if  ($this->UserExists($aUser)==true) { $this->AdminRegisterOkMsg();
                } else {$this->AdminRegisterFailMsg(); };
                };
				};
        }

        function UserRegistration()
        {
                // Имя пользователя
                $aUser = $_POST['user'];
                // Пароль
                $aPassword = $_POST['password'];
                // Если пользователь есть в базе, то выводим сообщение об ошибке
                if ($this->UserExists("$aUser") == true) {
                    
                    die();
                    $this->UserRegisterFailMsg();} else
                {
                // в обратном случае регистрация
                // Создаем ключ на основе пары имя пользователя - пароль
                 $aKey = $this->MakeKey($aUser, $aPassword);
           // Регистрируем пользователя
         $this->RegisterUser($aUser, $aKey, 'user');
                // Проверяем, была ли успешна регистрация
                if  ($this->UserExists($aUser)==true) { $this->UserRegisterOkMsg();
                } else {$this->UserRegisterFailMsg();

                };
                };
        }
        /**
         * Вносит в базу данных имя пользователя, ключ доступа и роль
         */
        public function RegisterUser($aUser, $aKey, $aRole) // БД: регистрация
        {
		$this->components['db']->setTable('users');
                $this->components['db']->Insert('user, ukey, role, online', "'$aUser', '$aKey', '$aRole', 1");
                                
        }
    public function RoleCheck($aUser, $aRole) // Проверка роли
        {
                $ok = ($this->GetRole($aUser) == $aRole);
                //$ok = ($this->GetOnline($aUser) == 1);
       return $ok;
        }
        public function DeleteUser($aUser) // Удаление пользователя, БД
        {
        		$this->components['db']->setTable('users');
                $this->components['db']->Delete("user='$aUser'");
        }

        public function LogOut() // Выход
        {
$this->MarkOffline();
                $_SESSION['ukey']='';;
                session_destroy();
        }
        function AdminLogout()
        {

$this->logOut();
$this->AdminLogOutMsg();
        }

function UserLogout()
        {
$this->logOut();

$this->UserLogOutMsg();
        }
        function  AdminActions()
        {
              
                $aTask = $this->GetAction();
               
                        switch ($aTask)
                        {
                                case 'clearlog' :
                                {

                                        $this->components['log']->ClearLogs();
                                        $this->visualized = "Все логи очищены";
                                        break;
                                };
                                case 'viewlog'   :
                                {                                        
                                    $log = $_POST['log'];
                                    $this->visualized =  "<h3>Просмотр лога $log</h3>";
                                    $this->visualized .= "<textarea style='width:100%;height:500px'>".$this->components['log']->ReadLog($log)."</textarea>";
                                                
                                        break;
                                };
                                case 'userdelete' :
                                {
                                        $aUser = $_POST['user'];
                                        $this->DeleteUser($aUser);
                                        $this->visualized = 'Удаление успешно: <a href="/admin/users">вернуться к списку пользователей</a>';
                                        break;
                                };
                                        case 'userrole' :
                                {
                                        $aUser = $_POST['user'];
                                        $aRole = $this->GetRole($aUser);
                                        $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/actions/userchange.tpl');
                                        $this->components['view']->SetVar('USER', $aUser);
                                        $arr = array ('admin'=>'Администратор', 'user'=>'Пользователь',
                                        'banned'=>'Заблокированный');
                                        $this->components['view']->SetVar('ROLE',
                                        $this->components['formitems']->SelectItem(
                                                $arr,
                                                $aRole,
                                                'role')
                                        );
                                        $this->components['view']->SetVar('ACTION', 'saverole');
                                        $this->components['view']->CreateView();
                                        $this->visualized = $this->components['view']->GetView();
                                        break;
                                };
                                        case 'saverole' :
                                {
                                        $aUser = $_POST['user'];
                                        $aRole = $_POST['role'];
                                        $this->components['db']->Update("role='$aRole'", "user='$aUser'");
                                        $this->visualized = 'Права пользователя были обновлены: <a href="/admin/users">вернуться к списку пользователей</a>';
                                        break;
                                };
                                default :
                                {
                                 
                                // Просмотр списка материалов
                                $this->visualized = '';
                                $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/items/userslist.tpl');
                                $this->components['db']->Plug();
                                $this->components['db']->setTable('users');
                                $this->components['db']->Select('user, ukey, role', '1=1');
                                
                                
                                $rows = $this->components['db']->Read();
                               
                                foreach ($rows as $i => $data )
                                {
                                        $this->components['view']->SetVar('USERNAME', $data['user']);
                                    $this->components['view']->SetVar('USERKEY', $data['ukey']);
                                        $this->components['view']->SetVar('USERROLE', $data['role']);
                                        $this->components['view']->CreateView();
                                        $this->visualized .= $this->components['view']->getView();
                                };

                                $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/tables/users.table.tpl');
                                $this->components['view']->SetVar('ITEMS', $this->visualized);
                                $this->components['view']->CreateView();
                                $this->visualized = $this->components['view']->GetView();
                                
                                break;
                                };

                                };
        }

        function AdminRun()
        {   
               
                $this->AdminActions();
                return $this->visualized;
        }

}
?>
