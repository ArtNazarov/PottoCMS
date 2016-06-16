<?php
if (!defined('APP')) {die('ERROR');};
 /**
 * \brief Модель "Публикация (новостной материал) сайта".
 * Позволяет добавлять материалы, узнавать информацию о публикации,
 * вносить правки и удалять страницу.
 * Требует наличия таблицы pages
 * Структура таблицы pages
 *
 * CREATE TABLE `~DB_PREFIX~pages` (
*  `id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
*  `title` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
*  `body` text COLLATE utf8_unicode_ci NOT NULL,
*  `category` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
*  `status` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'public',
*  `username` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
*  `created` datetime NOT NULL,
*  PRIMARY KEY (`id`)
* ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
*
* где ~DB_PREFIX~ - префикс таблиц
*/

 class SitePageItem
{
       /**
       * Заголовок материала
       * @var string
       */
	var $title; 
       /**
       *  Текст страницы
       * @var string
       */ 
	var $body; 
       /**
       * Категория страницы
       * @var string
       */ 
	var $category; 
       /**
       * Статус страницы
       * @var string
       */ 
	var $status; 
        /**
       * Имя пользователя
       * @var string
       */        
	var $username; 
       /**
       * Дата создания (обновления)
       * @var string 
       */
	var $created;  
        /**
         * \brief Очищает корзину
         * Удаляет из таблицы pages все записи со статусом bin
         * @param object $db_component  - ссылка на компонент SealDb
         */
	function clearBin(&$db_component)
	{
	$db_component->setTable('pages');
	$db_component->Delete("status='bin'");
	}
	
         /**
         * \brief УДАЛЯЕТ ИЗ БАЗЫ СТРАНИЦУ ПО ID.
         * Если страница имеет категорию bin (корзина), то она
         * удаляется полностью. В обратном случае категория
         * страница заменяется на bin (страница отправляется в корзину)
         * @param object $db_component - ссылка на компонент SealDb
         * @param string $aID - ID страницы
         */
	function delPage(&$db_component, $aID)
	{
		$db_component->setTable('pages');
		$status = $db_component->getCell("status", "id='$aID'");
		  if ($status=="bin") {
					$db_component->Delete("id='$aID'");
		  }
		  else
		   {
			   $db_component->Update("status='bin'", "id='$aID'");
		   }
		//
	}
	
        /**
         * \brief ПОМЕЩАЕТ В ПОЛЯ ОБЪЕКТА ИНФОРМАЦИЮ О СТРАНИЦЕ.
         * Выполняет запрос к таблице pages и присваивает
         * своим одноименным полям значения из соответствующей записи
         * @param object $db_component - ссылка на компонент SealDb
         * @param string $aID - ID страницы
         */
	function getPage(&$db_component, $aID)
	{
		$db_component->setTable('pages');
		$db_component->Select('title, body, status, category, username, created', "id='$aID'");
		$data = mysql_fetch_array($db_component->sql_result);
		$this->title = $data['title'];
		$this->body = $data['body'];
		$this->category = $data['category'];
		$this->status = $data['status'];
		$this->created = $data['created'];
		$this->username = $data['username'];
	}
	
        /**
         * \brief ОБНОВЛЯЕТ В БАЗЕ СТРАНИЦУ.
         * @param object $db_component - ссылка на компонент SealDb
         * @param string $aOldID - предыдущий ID страницы
         * @param string $aNewID - новый ID страницы
         * @param string $aTitle - заголовок страницы
         * @param string $aBody - текст страницы
         * @param string $aCategory - категория публикации 
         * @param string $aStatus - статус публикации
         * @param string $aUsername - имя пользователя
         * @param string $aCreated  - дата правки
         */
	function editPage(&$db_component, $aOldID, $aNewID, $aTitle, $aBody, $aCategory, $aStatus, $aUsername, $aCreated)
	{
		$db_component->setTable('pages');
		$db_component->Update("id='$aNewID', title='$aTitle', body='$aBody', category='$aCategory', status='$aStatus',
		username='$aUsername', created='$aCreated'", "id='$aOldID'");

	}
         /**
         * \brief ДОБАВЛЯЕТ В БАЗУ СТРАНИЦУ.
         * Вносит в таблицу pages соответствующую запись
         * @param object $db_component - ссылка на SealDb
         * @param string $aID - ID страницы
         * @param string $aTitle - заголовок публикации
         * @param string $aBody - текст публикации
         * @param string $aCategory - ID категории 
         * @param string $aStatus - статус
         * @param string $aUsername - имя пользователя
         * @param string $aCreated  - дата создания (изменения)
         */
	function addPage(&$db_component, $aID, $aTitle, $aBody, $aCategory, $aStatus, $aUsername, $aCreated)
	{
		$db_component->setTable('pages');
		$db_component->Insert("id, title, body, category, status, username, created",
                         "'$aID', '$aTitle', '$aBody', '$aCategory', '$aStatus', '$aUsername', '$aCreated'");
		//
	}
}

?>