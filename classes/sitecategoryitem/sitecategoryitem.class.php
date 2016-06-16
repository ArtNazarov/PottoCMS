<?php
if (!defined('APP')) {die('ERROR');};
 /**
 * \brief Модель "Категория материалов" .
 * Позволяет добавлять категории, узнавать информацию о категории,
 * вносить правки и удалять ее.
 * Требует наличия таблицы categories.
 * Структура таблицы categories:
 * CREATE TABLE `~DB_PREFIX~categories` (
 *  `category` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
 *  `cat_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
 *  `parent` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'root',
 *  `template` varchar(600) COLLATE utf8_unicode_ci NOT NULL,
 *   PRIMARY KEY (`category`)
 * ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
 * где ~DB_PREFIX~ - префикс таблиц
 */

class SiteCategoryItem
{
     /**
     * Ключ - значение поля category в таблице categories
     * @var $category string
     */
	var $category;
     /**
     * Имя категории
     * @var $cat_name string
     */        
	var $cat_name; 
    /**
     * ID категории верхнего уровня
     * @var $cat_parent string
     */                
	var $cat_parent;  
     /**
     * Шаблон оформления категории (URL)
     * @var $cat_template string
     */                        
	var $cat_template; 
        
         /**
         * \brief ДОБАВЛЯЕТ В БАЗУ КАТЕГОРИЮ.
         * Вставка новой записи в таблицу categories
         * @param object $db_component - ссылка на компонент SealDb
         * @param string $aCategory - id новой категории
         * @param string $aParent - id родительской категории 
         * @param string $aCat_name - имя категории
         * @param string $aCat_template - шаблон оформления категории и ее страниц
         */
        
	function addCategory(&$db_component, $aCategory, $aParent, $aCat_name, $aCat_template)
	{
		$db_component->setTable('categories');
		$db_component->Insert("category, parent, cat_name, template", "'$aCategory', '$aParent', '$aCat_name', '$aCat_template'");
		//
	}
        
	 /**
         * \brief ПОМЕЩАЕТ В ПОЛЯ ОБЪЕКТА ИНФОРМАЦИЮ О КАТЕГОРИИ.
         * На основании ключа $aCategory по данным таблицы categories заполняет одноименные поля
         * @param object $db_component - ссылка на SealDb
         * @param string $aCategory - ID категории
         */
	function getCategory(&$db_component, $aCategory)
	{
	   $db_component->setTable('categories');
	   $db_component->Select('category, parent, cat_name, template', "category='$aCategory'");
	   $data = $db_component->Read();
       $this->category = $data['category'];
  	   $this->cat_parent =  $data['parent'];
 	   $this->cat_name = $data['cat_name'];
 	   $this->cat_template = $data['template'];
	}
        /**
         * \brief Массив, передающийся в конструкторы списков выбора категории.
         * Возвращает массив, где ключ - ID категории, значение - имя категории
         * пустому ключу соответствует значение '/'
         * @param object $db_component - ссылка на SealDb
         * @return array[string] of string
         */
	function getCategoriesArray(&$db_component, $root_key)
	{
		$arr = array();
		$arr[''] = $root_key;
		$db_component->setTable('categories');
		$db_component->Select('cat_name, category', '1=1');
		while ($data = $db_component->Read())
		{
			$key = $data['category'];
			$value = $data['cat_name'];
			$arr[$key] = $value;
		}		
		return $arr;
	}

        /**
         * \brief ОБНОВЛЯЕТ В БАЗЕ КАТЕГОРИЮ.
         * Обновляет записи в таблицы categories и в соответствующих подчиненных таблицах,
         * где это необходимо
         * @param object $db_component - ссылка на SealDb.
         * @param string $aCategory - ID категории
         * @param string $aParent - ID родительской категории
         * @param string $aCat_name - имя категории
         * @param string $aTemplate - url шаблона оформления
         * @param string $aOld_Category - предыдущий ID этой категории до правки
         */
        function editCategory(&$db_component, $aCategory, $aParent, $aCat_name, $aTemplate, $aOld_Category)
	{
// Вносим изменения в основную таблицу
$db_component->setTable('categories');
$db_component->Update("category='$aCategory', parent='$aParent', cat_name='$aCat_name', template='$aTemplate'", "category='$aOld_Category'");

// Вносим изменения в подчиненную таблицу
$db_component->setTable('pages');
$db_component->Update("category='$aCategory'", "category='$aOld_Category'");

$db_component->setTable('categories');
$db_component->Update("parent='$aCategory'", "parent='$aOld_Category'");
	}
        /**
         * \brief Удаление категории.
         * Удаляет категорию из таблицы categories,
         * а материалы, которые в ней содержались,
         * переносит в раздел bin (корзина)
         * @param object $db_component - ссылка на SealDb
         * @param string $aCategory - ID категории
         */
	function deleteCategory(&$db_component, $aCategory)
	{
		$db_component->setTable('pages');
		$db_component->Update("status='bin'", "category='$aCategory'");
		$db_component->setTable('categories');
		$db_component->Update("parent='bin'", "parent='$aCategory'");
		$db_component->Delete("category='$aCategory'");
    }
}


?>