<?php
// Форум Potto CMS
if (!defined('APP')) {die('ERROR mink.class.php');};
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core/ClassFactory/ClassFactory.class.php';

// site.ru/index.php?do=forum&forum_action=[forums|themes|messages]

class Forum
{
/* var $kernel; // Ссылка на ядро */
var $components; // Компоненты
var $title; // Заголовок
var $conf_name; // Название конференции
	function __construct(array $params)
	{
		$this->components = null;
		$this->components['factory'] = new ClassFactory($params);

     $this->components['view'] =  $this->components['factory']->createInstance("TemplateTool", $params, 'Core');
	 $this->components['security'] = $this->components['factory']->createInstance("UserAuth", $params, 'Services');
	 $this->components['db'] =  $this->components['factory']->createInstance("DatabaseLayer", $params, 'Core');
	 $this->components['db']->Plug();
	 $this->conf_name = 'Мой форум на Potto :)';
	 $this->title = '';
	 
	 
	}
function __destruct()
{
 foreach ($this->components as $key => $value)
	  {
		  unset($this->components[$key]);
	  }
 unset($this->components);
}

function translitIt($str) 
{
    $tr = array(
        "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
        "Д"=>"D","Е"=>"E","Ж"=>"J","З"=>"Z","И"=>"I",
        "Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
        "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
        "У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"TS","Ч"=>"CH",
        "Ш"=>"SH","Щ"=>"SCH","Ъ"=>"","Ы"=>"YI","Ь"=>"",
        "Э"=>"E","Ю"=>"YU","Я"=>"YA","а"=>"a","б"=>"b",
        "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
        "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
        "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
        "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
        "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
        "ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya",
		" "=>"_", "!"=>'vs', '?'=>'vo', '#'=>'rs', '+'=>'pl', '-'=>'mn'
    );
    return strtr($str,$tr);
}

function GetForums()
{
$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/mink/mink.forum.tpl');
$this->components['db']->setTable('forums');
$this->components['db']->Select(' * ','1=1');
$items = 'Конференция<br/>';
$this->title = $this->conf_name;
$rows = $this->components['db']->Read(); foreach ($rows as $i=>$data)
{
$this->components['view']->SetVar('FORUM_URL', '/forum/'.$data['forum_url'].'/');
$this->components['view']->SetVar('FORUM_NAME', $data['forum_name']);
$this->components['view']->SetVar('THEMES_COUNT', $data['themes_count']);
$this->components['view']->SetVar('MESSAGES_COUNT', $data['messages_count']);
if ($this->components['security']->GetRole($this->components['security']->GetUsernameFromSession()) == 'admin')
{
$this->components['view']->SetVar('SPEC_LINKS', "<a href=/forum/delforum/".$data['forum_url'].">[удалить]</a>");
}
else
{
$this->components['view']->SetVar('SPEC_LINKS', '');
};
$this->components['view']->CreateView();
$items .= $this->components['view']->GetView();
}

$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/mink/mink.conf.forum.tpl');
$this->components['view']->SetVar('FORUM_NAME', $forum_name);
$this->components['view']->CreateView();
$form = $this->components['view']->GetView();

$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/mink/mink.forums.tpl');
$this->components['view']->SetVar('ITEMS', $items);
$this->components['view']->SetVar('FORM',
	 		$this->components['view']->Choice(
			$this->components['security']->UserChecking(),
			$form."<br/>".$this->components['security']->UserProfile(), 
			$this->components['security']->UserLogForm()
			));
$this->components['view']->CreateView();
return $this->components['view']->GetView();
}

function GetThemes($forum_url)
{
$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/mink/mink.theme.tpl');
$this->components['db']->setTable('forums');
$this->components['db']->Select('*',"forum_url='$forum_url'");
$data = $this->components['db']->Read();
$forum_name = $data['forum_name'];
$breadcrumbs = "<a href=/forum>Форумы</a> - <a href=/forum/$forum_url>$forum_name</a>";
$this->title = $this->conf_name . " $forum_name";
$this->components['db']->setTable('themes');
$this->components['db']->Select(' * ',"forum_url='$forum_url'");
$items = $breadcrumbs."<br/>";
$rows = $this->components['db']->Read(); foreach ($rows as $i=>$data)
{
$this->components['view']->SetVar('THEME_URL', '/forum/theme/'.$data['theme_url'].'/');
$this->components['view']->SetVar('THEME_NAME', $data['theme_name']);
$this->components['view']->SetVar('READS', $data['sd']);
$this->components['view']->SetVar('NEW_MESSAGE', $data['new_message']);
$this->components['view']->SetVar('REPLIES', $data['replies']);
$this->components['view']->SetVar('CREATED', $data['created']);

if ($this->components['security']->GetRole($this->components['security']->GetUsernameFromSession()) == 'admin')
{
$this->components['view']->SetVar('SPEC_LINKS', "<a href=/forum/deltheme/".$data['theme_url'].">[удалить]</a>");
}
else
{
$this->components['view']->SetVar('SPEC_LINKS', '');
};

$this->components['view']->CreateView();
$items .= $this->components['view']->GetView();
}

$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/mink/mink.conf.theme.tpl');
$this->components['view']->SetVar('USERNAME', $this->components['security']->GetUsernameFromSession());
$this->components['view']->SetVar('MESSAGE', '');
$this->components['view']->SetVar('THEME_URL', '');
$this->components['view']->SetVar('THEME_NAME', '');
$this->components['view']->SetVar('FORUM_URL', $forum_url);
$this->components['view']->CreateView();
$form = $this->components['view']->GetView();

$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/mink/mink.themes.tpl');
$this->components['view']->SetVar('ITEMS', $items);
$this->components['view']->SetVar('FORM',
	 		$this->components['view']->Choice(
			$this->components['security']->UserChecking(),
			$form."<br/>".$this->components['security']->UserProfile(), 
			$this->components['security']->UserLogForm()
			));
$this->components['view']->CreateView();
return $this->components['view']->GetView();
}

function GetMessages($theme_url)
{
$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/mink/mink.message.tpl');

$this->components['db']->setTable('themes');
$this->components['db']->Select(' * '," theme_url='$theme_url' " );
$data = $this->components['db']->Read();
$this->components['db']->Clear();

$theme_name = $data['theme_name'];
$forum_url = $data['forum_url'];
$sd = $data['sd']; 
$sd = (int)$sd + 1;
$this->components['db']->setTable('themes');
$this->components['db']->Update(" sd = $sd ", " theme_url='$theme_url' ");

$this->components['db']->setTable('forums');
$this->components['db']->Select(' * ',"forum_url='$forum_url'");
$data = $this->components['db']->Read();
$forum_name = $data['forum_name'];

$breadcrumbs = "<a href=/forum>Форумы</a> - <a href=/forum/$forum_url>$forum_name</a> - <a href=/forum/theme/$theme_url>$theme_name</a> ";
$breadcrumbs.="<!-- $sd -->";
$this->title = $this->conf_name . "$forum_name - $theme_name";
$this->components['db']->setTable('messages');
$this->components['db']->Select(' * ',"theme_url='$theme_url'  ORDER BY message_id ");
$items = $breadcrumbs."<br/>";
$rows = $this->components['db']->Read(); foreach ($rows as $i=>$data)
{
$this->components['view']->SetVar('MESSAGE_ID', $data['message_id']);
$this->components['view']->SetVar('MESSAGE', $data['message']);
$this->components['view']->SetVar('CREATED', $data['created']);
$this->components['view']->SetVar('USERNAME', $data['username']);
$this->components['view']->SetVar('DEVIZ', '<hr/>'.$this->components['security']->GetUserOption($data['username'], 'deviz'));
if ($this->components['security']->GetRole($this->components['security']->GetUsernameFromSession()) == 'admin')
{
$this->components['view']->SetVar('SPEC_LINKS', "<a href=/forum/delmessage/".$data['message_id'].">[удалить]</a>");
}
else
{
$this->components['view']->SetVar('SPEC_LINKS', '');
};



$this->components['view']->CreateView();
$items .= $this->components['view']->GetView();
}



$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/mink/mink.conf.message.tpl');
$this->components['view']->SetVar('USERNAME',  $this->components['security']->GetUsernameFromSession());





$this->components['view']->SetVar('MESSAGE', '');
$this->components['view']->SetVar('THEME_URL', $theme_url);
$this->components['view']->CreateView();
$form = $this->components['view']->GetView();
$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/mink/mink.messages.tpl');
$this->components['view']->SetVar('ITEMS', $items);
$this->components['view']->SetVar('FORM',
	 		$this->components['view']->Choice(
			$this->components['security']->UserChecking(),
			$form."<br/>".$this->components['security']->UserProfile(), 
			$this->components['security']->UserLogForm()
			));
$this->components['view']->CreateView();



return $this->components['view']->GetView();
}

function NewPost()
{
$username = $_POST['username'];
$message = strip_tags($_POST['message']);
$theme_url = $_POST['theme_url'];

$this->components['db']->setTable('messages');
$number = rand(1, 999999999);
$this->components['db']->Select(' MAX(message_id) as maxid ', ' 1 ');
$data = $this->components['db']->Read();
$message_id = $data['maxid'] + 1;
$created = date("Y-m-d h:m:s");
$this->components['db']->Insert('message_id, message, username, created, theme_url',
     "$message_id, '$message', '$username', '$created', '$theme_url'");

// Увеличить счетчик ответов в соотв. теме

$this->components['db']->setTable('themes');
$this->components['db']->Update('replies=replies+1', "theme_url='$theme_url'");
$this->components['db']->Select('forum_url', "theme_url='$theme_url'");
$data = $this->components['db']->Read();
$forum_url = $data['forum_url'];
// Увеличить счетчик в соотв. форуме
$this->components['db']->setTable('forums');
$this->components['db']->Update('messages_count=messages_count+1', "forum_url='$forum_url'");
	 
	 
	 
return "<a href=/forum/theme/$theme_url>Сообщение добавлено, вернуться в тему</a>";

}


function NewTheme()
{
$username = $_POST['username'];
$message = strip_tags($_POST['message']);
$theme_name = $_POST['theme_name'];
$forum_url = $_POST['forum_url'];
$theme_url = substr($this->translitIt($theme_name), 0, 254);

$this->components['db']->setTable('messages');
$number = rand(1, 999999999);
$this->components['db']->Select(' MAX(message_id) as maxid ', ' 1 ');
$data = $this->components['db']->Read();
$message_id = $data['maxid'] + 1;
$created = date("Y-m-d h:m:s");
$this->components['db']->Insert('message_id, message, username, created, theme_url',
     "$message_id, '$message', '$username', '$created', '$theme_url'");

$this->components['db']->SetTable('themes');
$this->components['db']->Insert('theme_url, theme_name, created, new_message, forum_url', 
								"'$theme_url', '$theme_name', '$created', '$created', '$forum_url'");	 
														
// Увеличить счетчик в соотв. форуме
$this->components['db']->setTable('forums');
$this->components['db']->Update('themes_count=themes_count+1', "forum_url='$forum_url'");														
$this->components['db']->Update('messages_count=messages_count+1', "forum_url='$forum_url'");																												
return "<a href=/forum/theme/$theme_url>Сообщение добавлено, вернуться в тему</a>";

}

function NewForum()
{
$forum_name = $_POST['forum_name'];
$forum_url = substr($this->translitIt($forum_name), 0, 254);
$this->components['db']->SetTable('forums');
$this->components['db']->Insert('forum_name, forum_url',
     "'$forum_name', '$forum_url'");
														
return "<a href=/forum/$forum_url>Форум создан</a>";

}

function DeleteMessage($message_id)
{
$this->components['db']->setTable('messages');
$this->components['db']->Delete("message_id=$message_id");

// Уменьшить счетчик ответов в соотв. теме

$this->components['db']->setTable('themes');
$this->components['db']->Update('replies=replies-1', "theme_url='$theme_url'");
$this->components['db']->Select('forum_url', "theme_url='$theme_url'");
$data = $this->components['db']->Read();
$forum_url = $data['forum_url'];
// Уменьшить счетчик в соотв. форуме
$this->components['db']->setTable('forums');
$this->components['db']->Update('messages_count=messages_count-1', "forum_url='$forum_url'");

return "<a href='/forum/'>Сообщение удалено...</a>";
}

function DeleteTheme($theme_url)
{
$this->components['db']->setTable('themes');
$this->components['db']->Select('replies', "theme_url='$theme_url'");
$data = $this->components['db']->Read();
$replies = $data['replies'];
$forum_url = $data['forum_url'];

$this->components['db']->Delete("theme_url='$theme_url'");

// Удалить все сообщения из данной темы
$this->components['db']->setTable('messages');
$this->components['db']->Delete("theme_url='$theme_url'");

// Уменьшить счетчик в соотв. форуме
$this->components['db']->setTable('forums');
$this->components['db']->Update("themes_count=themes_count-1", "forum_url='$forum_url'");
$this->components['db']->Update("messages_count=messages_count-$replies", "forum_url='$forum_url'");

return "<a href='/forum/'>Тема удалена...</a>";
}

function DeleteForum($forum_url)
{
$this->components['db']->setTable('forums');
$this->components['db']->Delete("forum_url='$forum_url'");
// Сначала найдем все темы с данного форума
$this->components['db']->setTable('themes');
$this->components['db']->Select(" * ", "forum_url='$forum_url'");
$arr_themes = array();
$i = 0;
$rows = $this->components['db']->Read(); foreach ($rows as $i=>$data)
{
$arr_themes[$i] = $data['theme_url'];
$i = $i+1;
};
$num = $i;
// Удаление тем
for ($i=0; $i<=$num; $i++)
{
$theme_url = $arr_themes[$i];
$this->components['db']->setTable('messages');
$this->components['db']->Delete("theme_url='$theme_url'");
$this->components['db']->setTable('themes');
$this->components['db']->Delete("theme_url='$theme_url'");
}
return "<a href='/forum/'>Форум удалён...</a>";
}


function Unknown()
{
return 'Неизвестное!';
}

function Run()
{
$std = $_SERVER['DOCUMENT_ROOT'].'/classes/mink/mink.std.tpl';
switch ($_GET['forum_action'])
{
 
 case 'forums' : {$body = $this->GetForums(); break;};
 case 'themes' : {$body = $this->GetThemes($_GET['forum']); break;};
 case 'messages' : {$body = $this->GetMessages($_GET['theme']); break; };
 case 'new_post' : {$body = $this->NewPost(); break;}
 case 'new_theme' : {$body = $this->NewTheme(); break;}
 case 'new_forum' : {$body = $this->NewForum(); break;}
 
 case 'del_message' : {$body = $this->DeleteMessage($_GET['id']); break;}
 case 'del_theme' : {$body = $this->DeleteTheme($_GET['id']); break;}
 case 'del_forum' : {$body = $this->DeleteForum($_GET['id']); break;}
 
 
 
 default : {$body = $this->Unknown();}
}
$this->components['view']->UseTpl($std);
$this->components['view']->SetVar('BODY', $body);
$this->components['view']->SetVar('TITLE', $this->title);
$this->components['view']->CreateView();
$this->components['view']->Publish();
}


}

?>