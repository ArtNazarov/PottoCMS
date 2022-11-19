<?php
if (!defined('APP')) {die('ERROR vombat.class.php');};
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core/ClassFactory/ClassFactory.class.php';

/**
 * Разработчики:
 * Copyright (c) 2011-2012, Potto CMS - Artem Nazarov. All rights reserved.
 * Visit <a href="http://artnazarov.ru/aboutpottocms">Potto CMS Site</a> to find more information about the component.
 */

/**
 * \brief Модуль комментариев.
 * Позволяет к любой странице сайта добавлять комментарии, а администратору
 * обрабатывать их (прятать, одобрять, удалять)
 */

class Comments
{
var $components;
// id_page == $_SERVER['REQUEST_URI']
function __construct(array &$params)
{
	$this->components = null;
    $this->components['factory'] = new ClassFactory($params);
    // Настройки базы данных
if (($params['db']!=null) && (is_object($params['db'])))
     { // Не создаем новый объект базы данных, используем переданный
     $this->components['db'] = &$params['db'];
     }
     else
     { // Конструируем новый объект базы данных
 	 $this->components['db'] = $this->components['factory']->createInstance("DatabaseLayer", $params, 'Core');
 	 };
	$this->components['db']->Plug();
	$this->components['view'] = $this->components['factory']->createInstance("TemplateTool", $params, 'Core'); // Подключаем шаблонизатор
	$this->components['options'] = $this->components['factory']->createInstance("CommonSettings", $params); // Подключаем общие настройки сайта
        $this->components['captcha'] = $this->components['factory']->createInstance("CaptchaTool", $params, 'Services'); // Подключаем капчу
}
function __destruct()
{
 foreach ($this->components as $key => $value)
	  {
		  unset($this->components[$key]);
	  }
	  unset($this->components);
}
 // Добавляет комментарий
function AddComment($aId_page)
{
	$aComment = strip_tags($_POST['comment']);
    $aUsername = $_POST['username'];
	$aCreated = $_POST['created'];
	$aRating = $_POST['rating'];
	$aStatus = 0;
	$this->components['db']->setTable('comments');
	do
	{
	$id_comment = rand(1, 999999);
	$this->components['db']->Select('id_comment', "id_comment=$id_comment");
	}
	while (mysqli_num_rows($this->components['db']->sql_result)!=0);

	$this->components['db']->Insert('id_comment,  username, id_page, comment, created, rating, status',
	 "$id_comment, '$aUsername', '$aId_page', '$aComment', '$aCreated', $aRating, $aStatus");
  $this->NoticeComment($aUsername, $aComment, $aId_page, $aRating);
}

function NoticeComment($username, $comment, $page, $rating)
{
	$to =  $this->components['options']->getOption('EMAIL_ADMIN');
	$subject = 'Новый комментарий от '.$username;
	$body = $comment."\r\n Страница комментария: ".$_SERVER['HTTP_HOST'].'/content/'.$page;
	$body .= $comment."Оценка: $rating";
	$from = $to;
	$headers =  "From: $from\r\n";
	$headers .= "Content-type: text/plain; charset=UTF-8\r\n";
	mail($to, $subject, $body, $headers );
}

// Удаляет комментарий
function DelComment()
{
	$id_comment = $_POST['id_comment'];
	$this->components['db']->setTable('comments');
	$this->components['db']->Delete("id_comment=$id_comment");

	$h = $this->admin_com_list();
	return $h;
}
// Отображает все комментарии к странице
function GetComments($aId_page)
{
	$com_body = '';
    $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/readers/items/comment.tpl');
    $this->components['db']->setTable('comments');
	$this->components['db']->Select('*', "id_page='$aId_page'");
	$comments_count = 0;
	$rating_total = 0;
	$rating_count = 0;
	if (mysqli_num_rows($this->components['db']->sql_result)!=0) {
	$rows = $this->components['db']->Read();
         foreach ($rows as $index => $data )
	{
	if ($data['status']>0)
	{
	    
	   	$this->components['view']->SetVar('COMMENT', $data['comment']);
     	$this->components['view']->SetVar('USERNAME', $data['username']);
     	$this->components['view']->SetVar('CREATED', $data['created']);
		$rating = $data['rating'];
		if ($data['rating']>0) {$rating = "+$rating";};
		$this->components['view']->SetVar('RATING', $rating);
		
		$comments_count ++;
		if ($rating!=0)
		{
		$rating_total = $rating_total + $rating;
		$rating_count ++;
		}
	
		$this->components['view']->CreateView();
		$com_body .= $this->components['view']->GetView();
	};
	};
	if ($rating_count == 0) {$avg_rate = 0;} 
	else {$avg_rate = round($rating_total / $rating_count, 3);};
	$com_body .= "</table>Всего комментариев: $comments_count<br/>Рейтинг:$rating_total; Средняя оценка: $avg_rate [учтено голосов: $rating_count ]";
	 } else $com_body = 'Комментов нет';

	return $com_body;
}
// Отображает форму добавления комментария
 function com_form($aUsername, $aID)
 {
  $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/readers/actions/addcomment.tpl');
  $this->components['view']->SetVar('USERNAME', $aUsername);
  $this->components['view']->SetVar('ID', $aID);
  $this->components['view']->SetVar('CREATED', date("Y-m-d H:i:s"));
  $this->components['view']->SetVar('CAPTCHA', $this->components['captcha']->FormCaptcha());
  $this->components['view']->CreateView();
  return $this->components['view']->GetView();
 }

function com_posted($aID)
{
	  $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/readers/actions/sys_message.tpl');
	  $this->components['view']->SetVar('SYS_TITLE', 'Сообщение');
	  $this->components['view']->SetVar('SYS_MESSAGE', 'Комментарий отправлен и будет доступен после одобрения модератором...');
	  $this->components['view']->SetVar('LINK_HREF', "$aID");
	  $this->components['view']->SetVar('LINK_TITLE', 'вернуться к странице...');
	  $this->components['view']->CreateView();
	  $this->components['view']->Publish();
}



// Результат правки комментария
function com_updated()
{
	 return 'Заглушка::Действие еще не запрограммировано! <a href="/admin/comments">Вернуться к странице комментариев</a>';
}

function com_accepted($aID)
{
  $this->components['db']->setTable('comments');
  $this->components['db']->Update('status=1', "id_comment='$aID'");
  return 'Комментарий одобрен. <a href="/admin/comments">Вернуться к странице комментариев</a>';
}

function com_declined($aID)
{
  $this->components['db']->setTable('comments');
  $this->components['db']->Update('status=0', "id_comment='$aID'");
  return 'Комментарий отклонен <a href="/admin/comments">Вернуться к странице комментариев</a>';
}


// Список комментов для админки
function admin_com_list()
{

    $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/items/comslist.tpl');
	$this->components['db']->setTable('comments');
	$this->components['db']->Select('comment', '1=1');
	if (mysqli_num_rows($this->components['db']->sql_result)!=0) {
  	 $db_page_names = new DatabaseLayer(null);
	 $db_page_names->Plug();
	 $db_page_names->setTable('pages');
  	 $l = '';

	 $this->components['db']->Select('*', "1=1");
	$rows = $this->components['db']->Read();
  	foreach ($rows as $index => $data )
	{
	   	$this->components['view']->SetVar('ID_COMMENT', $data['id_comment']);
		$this->components['view']->SetVar('USERNAME', $data['username']);
		$this->components['view']->SetVar('CREATED', $data['created']);
		
		if ($data['status']>0)
		{
		$this->components['view']->SetVar('STATUS', 'одобрен');
		}
		else
		{
		$this->components['view']->SetVar('STATUS', 'не одобрен');
		};
		
	 	$this->components['view']->SetVar('ID_PAGE', $data['id_page']);
		$this->components['view']->SetVar('COM_RATING', $data['rating']);
		$id_page = $data['id_page'];
		$this->components['view']->SetVar('PAGE_TITLE',  'Адрес страницы с комментарием');
     	$this->components['view']->SetVar('COM_TEXT', $data['comment']);
		$this->components['view']->CreateView();
		$l .= $this->components['view']->GetView();
	}

	$db_page_names->Done();
	 $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/tables/comments.table.tpl');
	 $this->components['view']->SetVar('ITEMS', $l);
	 $this->components['view']->CreateView();
	 $l = $this->components['view']->GetView();

	 } else $l = '';

   return $l;
}
}


?>