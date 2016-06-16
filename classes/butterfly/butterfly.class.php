<?php
if (!defined('APP')) {die('ERROR');};
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/masterfactory/masterfactory.class.php';

/**
 * Разработчики:
 * Copyright (c) 2011-2012, Potto CMS - Artem Nazarov. All rights reserved.
 * Visit <a href="http://artnazarov.ru/aboutpottocms">Potto CMS Site</a> to find more information about the component.
 */

/**
 * \brief Генератор RSS ленты для сайта
 * 1) Создает в корне сайта файл /rss.xml, в которую вносит записи о постах
 * из какой-то одной основной категории сайта
 * 2) в ответ на запрос /rss/CATEGORY.xml возвращает
 * ленту новостей из раздела сайта с id CATEGORY
 */

class Butterfly
{
   var $components;
   var $mod_path = '/classes/butterfly/';
   function __construct($params)
   {
	 $this->components['factory'] = new MasterFactory($params);
     // Настройки шаблонизатора
	 $this->components['view'] =  $this->components['factory']->createInstance("Lorius", $params);
	 // Настройки базы данных
 	  $this->components['db'] =  $this->components['factory']->createInstance("SealDB", $params);
 	  $this->components['db']->Plug();
	  $this->components['options'] =  $this->components['factory']->createInstance("WiseMonkey", $params);
   }
   function __destruct()
   {
     foreach ($this->components as $key => $value)
	  {
		  unset($this->components[$key]);
	  }
	  unset($this->components);
   }
   function CreateRss($aCategory)
   {
	 // Для страниц сайта
     $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].$this->mod_path.'rssitem.tpl');
	 $this->components['db']->setTable('pages');
 	 $this->components['db']->Select('id, title, body, created', "category='$aCategory' ORDER BY created DESC");
	 $items = "";
	 while ($data = @mysql_fetch_array($this->components['db']->sql_result))
	 {
 	   $url = $data['id'];
       $this->components['view']->SetVar('URL', 'http://'.$_SERVER["SERVER_NAME"]."/content/".$url);
       $this->components['view']->SetVar('TITLE', strip_tags(trim($data['title'])));
// Укороченный пост
//	   $this->components['view']->SetVar('TEXT', substr(strip_tags(trim($data['body'])), 1, 60));
	   $this->components['view']->SetVar('TEXT', $data['body']); // Пост полностью
       $this->components['view']->SetVar('DATE', $data['created']);
	   $this->components['view']->CreateView();
	   $items .= $this->components['view']->GetView()."\n\r";
	 }
	 $this->components['db']->Done();
     // Пишем ЛЕНТУ сайта
    $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].$this->mod_path.'rss.tpl');
	$this->components['view']->SetVar('ITEMS', $items);
	$this->components['view']->SetVar('SITE_NAME', $this->components['options']->GetOption('SITE_NAME'));
	$this->components['view']->SetVar('SITE_DESCRIPTION', $this->components['options']->GetOption('META_DESCRIPTION'));
	$this->components['view']->SetVar('SITE_URL', 'http://'.$_SERVER["SERVER_NAME"]);
    $this->components['view']->SetVar('UPDATE_DATE', date('r'));
	$this->components['view']->CreateView();
	$rss = $this->components['view']->GetView();
    $fp = fopen ($_SERVER['DOCUMENT_ROOT'].'/rss.xml', "w+");
    fwrite ($fp, $rss);
    fclose ($fp);
   }
   function PublishRss($aCategory)
   {
	 // Для страниц сайта
     $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].$this->mod_path.'rssitem.tpl');
	 $this->components['db']->setTable('pages');
 	 $this->components['db']->Select('id, title, body, created', "category='$aCategory' ORDER BY created DESC");
	 $items = "";
	 while ($data = @mysql_fetch_array($this->components['db']->sql_result))
	 {
 	   $url = $data['id'];
       $this->components['view']->SetVar('URL', 'http://'.$_SERVER["SERVER_NAME"]."/content/".$url);
       $this->components['view']->SetVar('TITLE', strip_tags(trim($data['title'])));
// Укороченный пост
//	   $this->components['view']->SetVar('TEXT', substr(strip_tags(trim($data['body'])), 1, 60));
	   $this->components['view']->SetVar('TEXT', $data['body']); // Пост полностью
       $this->components['view']->SetVar('DATE',$data['created']);
	   $this->components['view']->CreateView();
	   $items .= trim($this->components['view']->GetView());
	 }
	 $this->components['db']->Done();
     // Пишем ЛЕНТУ сайта
    $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].$this->mod_path.'rss.tpl');
	$this->components['view']->SetVar('ITEMS', $items);
	$this->components['view']->SetVar('SITE_NAME', $this->components['options']->GetOption('SITE_NAME'));
	$this->components['view']->SetVar('SITE_DESCRIPTION', $this->components['options']->GetOption('META_DESCRIPTION'));
	$this->components['view']->SetVar('SITE_URL', 'http://'.$_SERVER["SERVER_NAME"]);
    $this->components['view']->SetVar('UPDATE_DATE',date("r"));
	$this->components['view']->CreateView();
	$rss = $this->components['view']->GetView();
//    flush();
//    header("Content-Type: application/xml; charset=UTF-8");
    echo "$rss";
   }
}

?>