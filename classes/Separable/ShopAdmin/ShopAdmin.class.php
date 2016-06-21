<?php
if (!defined('APP')) {die('ERROR sklad.class.php');};
/**
 * \brief Складской учет.
 * Основной класс, реализующий учет товаров в подсистеме магазина.
 * Необходимые записи в файле /.htaccess для успешного функционирования модуля
 * # СКЛАД

* RewriteRule ^sklad/view/all/([0-9]+)$ index.php?do=sklad&action=view&page=$1
* RewriteRule ^sklad/view/all/([0-9]+)$ index.php?do=sklad&action=view&page=$1

* RewriteRule ^sklad/view/category/([a-zA-Z0-9_-]+)/([0-9]+)$ index.php?do=sklad&action=view&page=$2&category=$1
* RewriteRule ^sklad/view/category/([a-zA-Z0-9_-]+)/([0-9]+)/$ index.php?do=sklad&action=view&page=$2&category=$1

* RewriteRule ^sklad/vieworders/all/([0-9]+)$ index.php?do=sklad&action=vieworders&page=$1
* RewriteRule ^sklad/vieworders/all/([0-9]+)$ index.php?do=sklad&action=vieworders&page=$1

* RewriteRule ^sklad/vieworders/category/([a-zA-Z0-9_-]+)/([0-9]+)$ index.php?do=sklad&action=vieworders&page=$2&dtype=$1
* RewriteRule ^sklad/vieworders/category/([a-zA-Z0-9_-]+)/([0-9]+)/$ index.php?do=sklad&action=vieworders&page=$2&dtype=$1
* RewriteRule ^sklad/journal/([0-9]+)$ index.php?do=sklad&action=journal&page=$1
* RewriteRule ^sklad/journal/([0-9]+)/$ index.php?do=sklad&action=journal&page=$1
* RewriteRule ^sklad/([a-zA-Z0-9_-]+)$ index.php?do=sklad&action=$1
* RewriteRule ^sklad/([a-zA-Z0-9_-]+)/$ index.php?do=sklad&action=$1
* 
* RewriteRule ^sklad/item/([a-zA-Z0-9_-]+)/([a-zA-Z0-9_-]+)$ index.php?do=sklad&action=$1&artikul=$2
* RewriteRule ^sklad/item/([a-zA-Z0-9_-]+)/([a-zA-Z0-9_-]+)/$ index.php?do=sklad&action=$1&artikul=$2
* 
* RewriteRule ^sklad/category/([a-zA-Z0-9_-]+)/([a-zA-Z0-9_-]+)$ index.php?do=sklad&action=$1&category=$2
* RewriteRule ^sklad/category/([a-zA-Z0-9_-]+)/([a-zA-Z0-9_-]+)/$ index.php?do=sklad&action=$1&category=$2
* 
* RewriteRule ^sklad/orderdetails/([a-zA-Z0-9_-]+)/ index.php?do=sklad&action=orderdetails&operation=$1
* RewriteRule ^sklad/orderdetails/([a-zA-Z0-9_-]+)$ index.php?do=sklad&action=orderdetails&operation=$1
* 
* RewriteRule ^sklad/orderinplus/([a-zA-Z0-9_-]+)/ index.php?do=sklad&action=orderinplus&operation=$1
* RewriteRule ^sklad/orderinplus/([a-zA-Z0-9_-]+)$ index.php?do=sklad&action=orderinpluss&operation=$1
* 
* RewriteRule ^sklad/orderinminus/([a-zA-Z0-9_-]+)/ index.php?do=sklad&action=orderinminus&operation=$1
* RewriteRule ^sklad/orderinminus/([a-zA-Z0-9_-]+)$ index.php?do=sklad&action=orderinminus&operation=$1
* 
* RewriteRule ^sklad/orderdelete/([a-zA-Z0-9_-]+)$ index.php?do=sklad&action=orderdelete&operation=$1
* RewriteRule ^sklad/orderdelete/([a-zA-Z0-9_-]+)/$ index.php?do=sklad&action=orderdelete&operation=$1
* # УЧЕТ ЗВОНКОВ
* RewriteRule ^sklad/deletecall/([a-zA-Z0-9_-]+)$ index.php?do=sklad&action=deletecall&callid=$1
* RewriteRule ^sklad/deletecall/([a-zA-Z0-9_-]+)/$ index.php?do=sklad&action=deletecall&callid=$1
* 
* @see http://artnazarov.ru/aboutpottocms 
* 
* Требует наличия таблиц trade_sklad, trade_structure, trade_operations и trade_operations_details
* Структура таблиц
* 
* CREATE TABLE `~DB_PREFIX~trade_operations` (
*   `operation` varchar(255) NOT NULL,
* `username` varchar(255) NOT NULL,
*   `dtype` varchar(255) NOT NULL,
*   `agent` varchar(255) NOT NULL,
*   `date` datetime NOT NULL,
*   `status` varchar(255) NOT NULL,
*   PRIMARY KEY (`operation`)
* ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
* 
* 
* 
* CREATE TABLE `~DB_PREFIX~trade_operations_details` (
*   `operation` varchar(255) NOT NULL,
*   `artikul` varchar(255) NOT NULL,
*   `price` float NOT NULL,
*   `count` int(11) NOT NULL,
*   PRIMARY KEY (`operation`)
* ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
* 
* CREATE TABLE `~DB_PREFIX~trade_sklad` (
*   `artikul` varchar(255) NOT NULL,
*   `type` varchar(255) NOT NULL,
*   `description` text NOT NULL,
*   `note` text NOT NULL,
*   `count` int(11) NOT NULL,
*   `price` float NOT NULL,
* `photo` varchar(255) NOT NULL,
*   PRIMARY KEY (`artikul`)
* ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
* 
* 
* CREATE TABLE `~DB_PREFIX~trade_structure` (
*   `category` varchar(255) NOT NULL,
*   `catname` varchar(255) NOT NULL,
*   `parent` varchar(255) NOT NULL
* ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
* 
* где ~DB_PREFIX~ - префикс таблиц сайта
*/

require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core/ClassFactory/ClassFactory.class.php';
class ShopAdmin
{   
     /** 
     * Подчиненные компоненты     
     */
var $components; 
     /** 
     * Способ доставки товара
     */
var $status_const = array(''=>'Обычная', 'home'=>'На дом доставка', 'shop'=>'Магазин');
     /** 
     * Вид накладной
     */
var $dtype_const = array(''=>'Без статуса', 'plus'=>'Завоз', 'minus'=>'Продажа');
     /** 
     * Конструктор
     * @param $params - список параметров конструктора
     */
function __construct(&$params)
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
	$this->components['options'] = $this->components['factory']->createInstance("CommonSettings", $params, 'Controllers'); // Подключаем шаблонизатор
	$this->components['paginator'] = $this->components['factory']->createInstance("Paginator", $params, 'Helpers'); // Подключаем шаблонизатор	
	$this->components['formitems'] = $this->components['factory']->createInstance("FormItems", $params, 'Core'); // Подключаем шаблонизатор	
        $this->components['usr'] = $this->components['factory']->createInstance("UserAuth", $params, 'Services'); // Подключаем шаблонизатор	
}
  /** 
  * Деструктор
  */
function __destruct()
{
 foreach ($this->components as $key => $value)
	  {
		  unset($this->components[$key]);
	  }
	  unset($this->components);
}


  /**   
  * Строит SQL запрос на выборку товаров включая вложенные категории.
  * @param $category - строка, ID категории в таблице trade_sklad
  * @return string
  */
function ChildWhereInclude($category)
{
$zapros = " type = '$category' ";
$this->components['db']->setTable("trade_structure");
$this->components['db']->Select("category", "parent='$category'");
$rows = $this->components['db']->Read(); foreach ($rows as $i=>$data)
{
$i = $data['category'];
$zapros = $zapros." OR (type = '$i') ";
};
return $zapros;
}

  /**   
  * Хлебные крошки, навигация по разделам сайта.
  * Возвращает последовательность гиперссылок вида Корень,Родительская категория, Текущая категория
  * @param $aCategory (string) Строка, ID текущей категории.
  * @return string 
  */
 function breadcrumbs($aCategory) 
	{

	// Хлебные крошки
	$h = '';
	$Category = $aCategory;
	$Parent = '';
    $Cat_name = '';
	$num_iter = 3;
	for ($i=1; $i <= $num_iter; $i++) {

	$this->components['db']->setTable('trade_structure');
    $this->components['db']->Select('*', "category='$Category'");
	$rows = $this->components['db']->Read(); foreach ($rows as $i=>$data)
	{
    $Cat_name = $data['catname'];
	$Parent =  $data['parent'];
	};
   
    $h = " →  <a href=/sklad/view/category/$Category/1>$Cat_name</a> ".$h;
	if ($Parent == '') break;
    if ($Parent == 'root') break;
	$Category = $Parent;
	$num_iter--;
	 };
	 $h = '<a href=/sklad/view/>Общий список товаров</a>&nbsp;'.$h;
	 if ($h!='') return $h;
	}

  /**   
  * Список категорий.
  * Возвращает ассоциативный массив ID категории -> Название категории
  * при этом пустому ключу соответствует основной (корневой) раздел.
  * @param $aCategory (string) Строка, ID текущей категории
  * @return array[string ID] of string Name
  */
function getCategoriesArray() // Массив категорий
	{
		$arr = null;
		$this->components['db']->setTable('trade_structure');
		$this->components['db']->Select('catname, category', '1=1');
		$arr['']='Основной список товаров';
		$rows = $this->components['db']->Read();
  	        foreach ($rows as $index => $data )
		{
			$key = $data['category'];
			$value = $data['catname'];
			$arr[$key] = $value;
		};
		
		return $arr;
	}
  /**   
  * Селектор категорий
  * Возвращает html разметку списка выбора категории товара
  * @param $aCode (string) Переменная шаблона
  * @param $category (string) ID категории, выбираемой в списке
  * @return HTML-string
  * @see getCategoriesArray()  
  */	
function CategorySelector($code, $category) // Селектор категорий
{
$arr = $this->getCategoriesArray();
$this->components['view']->SetVar($code,
           			$this->components['formitems']->SelectItem(
								$arr,
								$category,
								'type'));
}


  /**   
  * Вспомогательный обработчик.
  * Разрезает в каждой записи таблицы trade_sklad описание товара из поля description 
  * на краткое и полное описание, помещая их соответственно в поля captiontxt и
  * description. При этом изначально краткое и полное описание в поле description
  * до запуска обработчика должны быть отделены двумя 
  * @param $aCode (string) Переменная шаблона
  * @param $category (string) ID категории, выбираемой в списке
  * @return HTML-string
  * @see getCategoriesArray()  
  */

function ProcessDb()
{
echo 'Gathering items at store...';
    $this->components['db']->SetTable('trade_sklad');
    $this->components['db']->Select('*', '1 = 1');
    $replacements = array();
    $rows = $this->components['db']->Read(); foreach ($rows as $i=>$data)
    {
                    
           $captiontxt = $data['captiontxt'];
           $newcaptiontxt = strip_tags($captiontxt);           
           $replacements[$data['artikul']] = array();
           $replacements[$data['artikul']]['captiontxt']  = $captiontxt;
           $replacements[$data['artikul']]['newcaptiontxt']  = $newcaptiontxt;          
    };
   echo 'Update items...'; 
    foreach ($replacements as $key => $arr)
    {
        $captiontxt = $arr['captiontxt'];
        $newcaptiontxt = $arr['newcaptiontxt'];
        $artikul = $key;
        $description = $arr['description'];
        $this->components['db']->Update("captiontxt='$newcaptiontxt'", "artikul='$artikul'");        
    }
    echo "Proccessing done...";
    
    }
	
// =================== База данных

/**
 * Записывает в базу новый товар.
 * Данные новой записи вносятся в таблицу trade_sklad.
 * Ввод поступает из одноименных полей массива $_POST
 */    
    
function aNew() 
{
	$this->components['db']->setTable('trade_sklad');
	$artikul =$_POST['artikul'];
	$description = $_POST['description'];
        $captiontxt = $_POST['captiontxt'];
	$price = $_POST['price'];
	$count = $_POST['count'];
	$type = $_POST['type'];
	$photo = $_POST['img_photo'];
	$note = $_POST['note'];
	$tags = $_POST['tags'];
        $see_also = $_POST['see_also'];
	$this->components['db']->Insert('artikul, description, price, count, type, photo, note, tags, captiontxt, see_also',
	"'$artikul', '$description', $price, $count, '$type', '$photo', '$note', '$tags', '$captiontxt', '$see_also' ");
	// Обработать теги!
	
	return "Внесено! <a href='/sklad/view'>К списку товаров на складе</a> или <a href='/sklad/fnew'>Добавить еще один товар?</a>";
}

/**
 * Записывает новую группу товаров.
 * Данные новой записи вносятся в таблицу trade_structure.
 * Ввод поступает из одноименных полей массива $_POST
 */  

function aNewType() // 
{
	$this->components['db']->setTable('trade_structure');
	$category =$_POST['category'];
	$catname = $_POST['catname'];
	$parent = $_POST['type'];
	
	$this->components['db']->Insert('category, catname, parent',
	"'$category', '$catname', '$parent'");
		return "Группа товаров зарегистрирована! <a href='/sklad/view'>К списку товаров на складе</a>";
}

/**
 * Очищает накладную
 * Накладная представлена полями ассоциативного массива $_SESSION['bill']
 * Очистка осуществляется обнулением поля $_SESSION['bill']['count']
 */

function clearbill() // 
{
	$_SESSION['bill']['count'] = 0;
	return "Накладная очищена! <a href='/sklad/view'>Выбрать другие товары?</a>";		
}  

/**
 * Добавляет товар в форму накладной, но не проводит накладную.
 * Накладная представлена полями ассоциативного массива $_SESSION['bill']
 * В массиве информация о товаре накладной хранится в двух полях
 * $_SESSION['bill']["item".$nomer]['count'] и $_SESSION['bill']["item".$nomer]['count']
 * где $nomer - это номер товара в накладной, принимающий значения от 1 до
 * $_SESSION['bill']['count']
 * Если в массиве уже есть запись о товаре, то плюсуется его количество
 * Если в массиве нет записи о товаре, число позиций накладной увеличивается
 * и регистрируется артикул товара и количество единиц учета товара
 * Данные поступают из одноименных полей массива $_POST
 */

function addtobill() // 
{
$found = 0;
isset($_SESSION['bill']['count']) ? $bill_count = $_SESSION['bill']['count'] : $bill_count = 0;
 for ($i=1; $i<=$bill_count; $i++)
 {
 if ($_POST['artikul'] == $_SESSION['bill']["item".$i]["artikul"])
   {
     $found = $i;
	 break;
   };
 };
 if ($found==0)
 {
	$_SESSION['bill']['count'] = $_SESSION['bill']['count'] + 1; // Увеличить число товаров в накладной
    $nomer = $_SESSION['bill']['count'];
	$_SESSION['bill']["item".$nomer]['artikul'] = $_POST['artikul'];
	$_SESSION['bill']["item".$nomer]['count'] = $_POST['count'];
}
else
 {
 $_SESSION['bill']["item".$found]['count'] = $_SESSION['bill']["item".$found]['count'] + 1;
 }
	return "Товар добавлен в накладную. Перейти к <a href='/sklad/fprovodka'>проводке накладной</a> или <a href='/sklad/view'>продолжить заполнение накладной</a>?";		
}


/**
 * Выполняет проводку (регистрацию) накладной, но не изменяет остатки на складе
 * Накладная представлена полями ассоциативного массива $_SESSION['bill']
 * Вносятся связанные записи в таблице trade_operations и trade_operations_details
 * В trade_operations_details добавляется информация о товарах,
 * участвующих в операция, а в trade_operations - информация об участниках и типе
 * операции, адресе доставки и т.п. обобщенных сведениях
 */

function provodka() 
{
$datestr = date('w_d_Y_m');
if ($_SESSION['bill']['count']>0)
 { 
   $items = "";
   
   
   
   $operation = 'op'.rand(1,99).'d'.$datestr;
  
   for ($i=1; $i<=$_SESSION['bill']['count']; $i++)
     {
		 $artikul = $_SESSION['bill']["item".$i]['artikul'];	 
 		 $count = $_SESSION['bill']["item".$i]['count'];	 
		 // Узнаем информацию о товаре из базы	 
		 $artikul = $_SESSION['bill']["item".$i]['artikul'];
		 
		 $this->components['db']->setTable('trade_sklad');
		 
	     $this->components['db']->Select('artikul, description, type, price', "artikul='$artikul'");

	$data = $this->components['db']->Read();
	
	  $price =  $data['price'];
		
	$this->components['db']->setTable('trade_operations_details');
	
	$this->components['db']->Insert("operation, artikul, price, count", 
        "'$operation', '$artikul', $price, $count");
		 		 		 
		  		 
	
	 };
  $this->components['db']->setTable('trade_operations');
  $dtype = $_POST['dtype'];
  $agent = $_POST['agent'];
  $date = $datestr;
  $username = $this->components['usr']->GetUsernameFromSession();
  $status = 'ОЖИДАНИЕ';
  $this->components['db']->Insert("operation, date, agent, dtype, username",  "'$operation', '$date', '$agent', '$dtype', '$username'");   
	 };
	 // Сбрасываем накладную
	$_SESSION['bill']['count'] = 0;
	return "Проводка выполнена. <a href='/sklad/view'>Заполнить накладную</a> или <a href='/sklad/vieworders/all/1'>посмотреть операции</a>?";		
}

/**
 * Правка товара
 * Обновляет сведения о товаре в таблице trade_sklad
 * Данные берутся из одноименных полей массива $_POST
 * При смене ключа обновляются данные в подчиненных таблицах
 */

function Edit() // 
{
	$this->components['db']->setTable('trade_sklad');
	$artikul = $_POST['artikul'];
	$description = $_POST['description'];
	$price = $_POST['price'];
        $captiontxt = $_POST['captiontxt'];
	$count = $_POST['count'];
	$type = $_POST['type'];
	$photo = $_POST['img_photo'];
	$note = $_POST['note'];
	$old_artikul = $_POST['old_artikul'];
	$tags = $_POST['tags'];
	$see_also = $_POST['see_also'];
	$this->components['db']->Update("artikul='$artikul', type='$type', description='$description', price=$price, count=$count, photo='$photo', note='$note', tags='$tags', 
see_also='$see_also', captiontxt='$captiontxt'",
	"artikul = '$old_artikul'");
	
	// Целостность данных
	// Обновить ссылки в корзине, в накладной, в журнале детализаций операций и в журнале продаж
	// В корзине и в накладной на потом
	
	$this->components['db']->setTable('trade_operations_details'); // Детализации
	$this->components['db']->Update("artikul='$artikul'", "artikul = '$old_artikul'");
	$this->components['db']->setTable('trade_sales'); // Продажи
	$this->components['db']->Update("artikul='$artikul'", "artikul = '$old_artikul'");
	
	
	
	
		return "Правки сделаны. <a href='/sklad/view'>К списку товаров на складе</a>
или  <a href='/shop/buyer/viewitempage/$artikul'>просмотрим страницу на витрине</a> ?
        А может, <a href=/sklad/item/fedit/$artikul>редактировать продолжим?</a>
        ";                   

}

/**
 * Правка типа (категории товара)
 * Обновляет сведения о разделах магазин в таблице trade_structure
 * Данные берутся из одноименных полей массива $_POST
 * При смене ключа обновляются данные в подчиненных таблицах
 */

function EditType() 
{
	$this->components['db']->setTable('trade_structure');
	$category =$_POST['category'];
	$catname = $_POST['catname'];
	$parent = $_POST['type'];
	$old_category = $_POST['old_category'];
	$this->components['db']->Update("category='$category', catname='$catname', parent='$parent'",
	"category = '$old_category'");
	
	// Целостность данных
	// Обновить ссылки в корзине, в накладной, в списке товаров
	// В корзине и в накладной на потом
	
	$this->components['db']->setTable('trade_sklad'); // В журнале складского учёта товара
	$this->components['db']->Update("type='$category'", "type='$old_category'");
	
	
		return "Группа товаров обновлена! <a href='/sklad/view'>К списку товаров на складе</a>";
}

/**
 * Удаляет тип товара (категорию товара)
 * Изымает сведения о разделах магазина из таблицы trade_structure
 * Данные берутся из одноименных полей массива $_POST
 * Удаляет также все товары из данной категории из таблицы trade_sklad
 */

function DelType() 
{	
	$category =$_POST['category'];	
        $this->components['db']->setTable('trade_structure');
	$this->components['db']->Delete("category='$category'");
        $this->components['db']->setTable('trade_sklad');                
        $this->components['db']->Delete("type='$category'");	
        return "Группа товаров удалена! <a href='/sklad/view'>К списку товаров на складе</a>";
}

/**
 * Получает имя пользователя по ключу сессии
 * Обращается к таблице users
 */


function GetUsernameFromSession() 
{
        $this->components['db']->setTable('users');
        $ukey = $_SESSION['ukey'];
        $username = $this->components['db']->getCell('user', "ukey='$ukey'");
        return $username;
}

/**
 * Выдает сообщение об ошибочном, недоступном действии
 * Код действия (строка) берется из $_GET['action']
 */


function Unknown()
{
    $operation = $_GET['action'];
	return "ОШИБКА! Неизвестное действие $operation... <a href='/sklad/view'>К списку товаров на складе</a>";
}

/**
 * Возвращает HTML текст со cписком категорий товаров
 * Ввод поступает из поля $_GET['category']
 * Обращается к таблице trade_structure
 * @return string
 */


function Categories() 
{
$items = "";
$this->components['db']->setTable('trade_structure');
	 
isset($_GET['category']) ? $category = $_GET['category'] : $category = "";
if ($category!="")
{
$where = "parent = '$category'";
}
else
{
$where = "parent = ''";
};

	$this->components['db']->Select('*', "$where");
	
	$rows = $this->components['db']->Read(); foreach ($rows as $i=>$data)
	{
	   	$category = $data['category'];
		$catname = $data['catname'];
		isset($_GET['category']) ? $mycategory = $_GET['category'] : $mycategory = "";
if ($category!=$mycategory)
{
		$items .= "<a href='/sklad/view/category/$category/1'>$catname</a>";
		} else
		{
		$items .= "<b>$catname</b>";
		};
$items.="&nbsp;<a href='/sklad/category/fedittype/$category' title='Настройка раздела' style='text-decoration:none'><img style='text-decoration:none' src='/images/options.gif' border='0' /></a><br/>";		
	};
if ($items=='') {$items = '<i>Вложенных разделов нет<i>';};	
return $items;
}


/**
 * Просмотр журнала операций
 * Возвращает HTML строку (таблицу) со списком заказов, продаж и завозов
 * Ввод поступает из поля $_GET['category']
 * Обращается к таблице trade_operations
 * @return string
 */

function ViewOrders()  
{

    isset($_GET['category']) ? $category = $_GET['category'] : $category = "";
	
    $where = urldecode($category);
	
	if ($where == "") {$where = "1=1";} else {$where = "type = '$where'";};
	
	$items = '';
    $articles = 3;
	if ($category == "")
	{
	$link = "/sklad/vieworders/all/";
	}
	else
	{
	$link = "/sklad/vieworders/category/$category/";
	};
	$page = $_GET['page'];
	if ($page<=0) {$page=1;};
	$from_page = $articles * ($page - 1);
	$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/sklad/sklad.order.short.items.tpl');
    $this->components['db']->setTable('trade_operations');
	 
	$this->components['db']->Select('operation, dtype, agent, username, status, date ', "$where ORDER BY operation LIMIT $from_page, $articles");
	if (mysql_num_rows($this->components['db']->sql_result)!=0) 
	{
	$rows = $this->components['db']->Read(); foreach ($rows as $i=>$data)
	{
	   	$this->components['view']->SetVar('OPERATION', $data['operation']);
                
	   	$this->components['view']->SetVar('DTYPE', $this->dtype_const[$data['dtype']]);
     	$this->components['view']->SetVar('AGENT', $data['agent']);
     	$this->components['view']->SetVar('USERNAME', $data['username']);
	    $this->components['view']->SetVar('DATE', $data['date']);
            
     	$this->components['view']->SetVar('STATUS', $this->status_const[$data['status']]);
		$this->components['view']->CreateView();
		$items .= $this->components['view']->GetView();
	}

      $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/sklad/sklad.order.table.tpl');
	
  $this->components['paginator']->Pages($this, $page, 'dtype', $category, $articles, $link, "trade_operations", "1=1");
	 
	
  	$this->components['view']->SetVar('ITEMS', $items);
	
	$categories = $this->Categories();
	$this->components['view']->SetVar('CATEGORIES', $categories);
	
	$this->components['view']->CreateView();
	$ui = $this->components['view']->GetView();
	return $ui;
	} else {$ui = "Движений нет!"; return $ui; };
	
	
}


/**
 * Просмотр остатков на складе
 * Возвращает HTML строку (таблицу) со списком товаров
 * Ввод поступает из поля $_GET['category'] и $_GET['page']
 * Обращается к таблице trade_sklad
 * @return string
 */


function View()  
{

    isset($_GET['category']) ? $category = $_GET['category'] : $category = "";
	 
	$breadcrumbs = $this->breadcrumbs($category);
	
    $where = urldecode($category);
	
	if ($where == "") {$where = "1=1";} else {$where = $this->ChildWhereInclude($category);};
	
	$items = '';
        $articles = $this->components['options']->GetOption('SKLAD_ITEMSPERPAGE');
	if ($category == "")
	{
	$link = "/sklad/view/all/";
	}
	else
	{
	$link = "/sklad/view/category/$category/";
	};
	isset($_GET['page']) ? 	$page = $_GET['page'] : $page = 1;
	if ($page<=0) {$page=1;};
	$from_page = $articles * ($page - 1);
	$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/sklad/sklad.items.tpl');
    
    $this->components['db']->setTable('trade_structure');
    // Получаем имена категорий
    $this->components['db']->Select('*', "1=1");
    $nameofcategory = array();
    $rows = $this->components['db']->Read();
    foreach ($rows as $i=>$data)
    {
        $nameofcategory[$data['category']] = $data['catname'];
    };    
    $this->components['db']->setTable('trade_sklad');
	 
	$this->components['db']->Select('*', "$where ORDER BY description LIMIT $from_page, $articles");
	if (mysql_num_rows($this->components['db']->sql_result)!=0) 
	{
        $rows = $this->components['db']->Read();
	foreach ($rows as $i => $data)
	{
	   	$this->components['view']->SetVar('ARTIKUL', $data['artikul']);
	   	$this->components['view']->SetVar('TYPE',
                        
                        "<a href=/sklad/view/category/".$data['type']."/1>".
                        $nameofcategory[$data['type']] . "</a>");
     	$this->components['view']->SetVar('DESCRIPTION', $data['captiontxt']);
     	$this->components['view']->SetVar('PRICE', $data['price']);
     	$this->components['view']->SetVar('COUNT', $data['count']);
		$this->components['view']->SetVar('NOTE', $data['note']);
		
		if ($data['photo']!="")  
		{$this->components['view']->SetVar('PATH', $data['photo']); }
		else {$this->components['view']->SetVar('PATH', '/images/no-photo.jpg');};
		
		$this->components['view']->CreateView();
		$items .= $this->components['view']->GetView();
	}

      $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/sklad/sklad.table.tpl');
	
  $this->components['paginator']->SPages($this, $page, $where, $category, $articles, $link, "trade_sklad", "1=1");
	 
	
  	$this->components['view']->SetVar('ITEMS', $items);
	
	$categories = $this->Categories();
        if (isset($_SESSION['bill']))
        {
                isset($_SESSION['bill']['count']) ? $bc = $_SESSION['bill']['count'] : $bc = '0';
        };
        if ($bc == 0) {$bc = '0';};
        $this->components['view']->SetVar('BILL_COUNT', $bc);
	$this->components['view']->SetVar('CATEGORIES', $categories);
	$this->components['view']->SetVar('BREADCRUMBS', $breadcrumbs);
	$this->components['view']->CreateView();
	$ui = $this->components['view']->GetView();
	return $ui;
	} else {$ui = "Товаров в этом разделе на складе нет!"; return $ui; };
	
	
	
}

/**
 * Форма добавления нового товара на склад
 * Возвращает HTML строку (таблицу) со списком товаров
 * Заполняет шаблон /classes/sklad/sklad.editor.tpl
 * @return string
 */

 function FNew()  
 {
		$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/sklad/sklad.editor.tpl');
	  	$this->components['view']->SetVar('ARTIKUL', '');
                $this->components['view']->SetVar('CAPTIONTXT', '');
     	$this->components['view']->SetVar('DESCRIPTION', '');
     	$this->CategorySelector("CATEGORY", "");
     	$this->components['view']->SetVar('PRICE', '');
     	$this->components['view']->SetVar('COUNT', '');
        $this->components['view']->SetVar('TAGS', '');
        $this->components['view']->SetVar('SEE_ALSO', '');
	$this->components['view']->SetVar('PHOTO', 
			$this->components['formitems']->ImagesSelector('/fotos/', '', 'photo')
			);
		$this->components['view']->SetVar('NOTE', '');
		$this->components['view']->SetVar('ZPHOTO', 'нет');
				$this->components['view']->SetVar('FORM_ACTION', 'new');
							$this->components['view']->SetVar('BUTTON_NAME', 'Добавить этот артикул');
		$this->components['view']->CreateView();
  	$ui = $this->components['view']->GetView();
	return $ui;
 }
 
 /**
 * Форма регистрации группы товаров
 * Возвращает HTML строку (таблицу) со списком товаров
 * Заполняет шаблон '/classes/sklad/sklad.type.editor.tpl'
 * @return string
 */
 
  function FNewType() 
 {
		$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/sklad/sklad.type.editor.tpl');
	  	$this->components['view']->SetVar('CATEGORY', '');
     	$this->components['view']->SetVar('OLD_CATEGORY', '');
     	$this->components['view']->SetVar('CATNAME', '');
        $this->components['view']->SetVar('DELSTYLE', 'display:none');
     	$this->CategorySelector("PARENT", "");	
		$this->components['view']->SetVar('FORM_ACTION', 'newtype');
		$this->components['view']->SetVar('BUTTON_NAME', 'Зарегестировать группу товаров');
		$this->components['view']->CreateView();
  	$ui = $this->components['view']->GetView();
	return $ui;
 }
 
 
 /**
 * Форма правки товара
 * Возвращает HTML строку (таблицу) со списком товаров
 * Заполняет шаблон '/classes/sklad/sklad.editor.tpl'
 * @return string
 */
 
 
  function FEdit()  
 {
	$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/sklad/sklad.editor.tpl'); 
	$this->components['db']->setTable('trade_sklad');
	$artikul = $_GET['artikul'];
	$this->components['db']->Select('*', "artikul='$artikul'");
	$this->components['view']->SetVar('FORM_ACTION', 'edit');
	$data = $this->components['db']->Read()[0];
	
	   	$this->components['view']->SetVar('ARTIKUL', $data['artikul']);
     	$this->components['view']->SetVar('DESCRIPTION', $data['description']);
        $this->components['view']->SetVar('CAPTIONTXT', $data['captiontxt']);
     	$this->components['view']->SetVar('PRICE', $data['price']);
		$this->components['view']->SetVar('TAGS', $data['tags']);
		$this->components['view']->SetVar('SEE_ALSO', $data['see_also']);
	
     	$this->components['view']->SetVar('COUNT', $data['count']);
		$this->components['view']->SetVar('NOTE', $data['note']);
		$this->components['view']->SetVar('ZPHOTO', '<img height="64" src='.$data['photo'].' /> ');
	  $this->components['view']->SetVar('PHOTO', 
			$this->components['formitems']->ImagesSelector('/fotos/', $data['photo'], 'photo')
			);
	
		$this->CategorySelector("CATEGORY", $data['type']);	
			$this->components['view']->SetVar('ACTION', 'edit');
			$this->components['view']->SetVar('BUTTON_NAME', 'Внести эти изменения');
	$this->components['view']->CreateView();
  	$ui = $this->components['view']->GetView();
	return $ui;
 }
 
 /**
 * Форма правки типа товара
 * Возвращает HTML строку (таблицу) со списком товаров
 * Заполняет шаблон '/classes/sklad/sklad.type.editor.tpl'
 * @return string
 */
 
 
   function FEditType()  
 {
	$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/sklad/sklad.type.editor.tpl'); 
	$this->components['db']->setTable('trade_structure');
	$category = $_GET['category'];
	$this->components['db']->Select('category, catname, parent', "category='$category'");
        $data = $this->components['db']->Read()[0];
        $pc = $data['parent'];
        $this->components['db']->Select('category, catname, parent', "category='$category'");
	$this->components['view']->SetVar('FORM_ACTION', 'edittype');
        $rows = $this->components['db']->Read();
	foreach ($rows as $i => $data)
	{
	$this->components['view']->SetVar('CATEGORY', $data['category']);
     	$this->components['view']->SetVar('PARENT', $data['parent']);
     	$this->components['view']->SetVar('CATNAME', $data['catname']);		
	}
 	$this->CategorySelector("PARENT", $pc);
        $this->components['view']->SetVar('DELSTYLE', 'display:inline');
	$this->components['view']->SetVar('BUTTON_NAME', 'Внести эти изменения');
	$this->components['view']->CreateView();
  	$ui = $this->components['view']->GetView();
	return $ui;
 }
 
 /**
 * Форма выбора ценников к печати
 * Возвращает HTML строку (таблицу) со списком товаров
 * Заполняет шаблон '/classes/sklad/sklad.print.price.tpl'
 * выбирая товары из таблицы trade_sklad
 * @return string
 */ 
 
 
 function fprintprice()  
 {
 $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/sklad/sklad.print.price.tpl');
 
 // Выбираем все товары
 $this->components['db']->setTable('trade_sklad');
 $this->components['db']->Select('*', '1=1');
 $arr = array();
 $selected = array();
 $c = 0;
 $rows = $this->components['db']->Read(); foreach ($rows as $i=>$data)
 {
  $arr[$data['artikul']] = $data['description'];
 // $selected[$c] = $data['artikul']; $c++; 
 }
 // Флажки checkbox
 
 $this->components['view']->SetVar('TOVAR_SELECTOR', 
 $this->components['formitems']->CheckboxItems($arr, $selected, 'tovars'));
 $this->components['view']->SetVar('ACTION', 'printprice');
 $this->components['view']->CreateView();
 $ui = $this->components['view']->GetView();
 return $ui;
 }
 
 
 /**
 * Распечатка ценников
 * создает к корне сайта файл для передачи на принтер /price.html
 * используя шаблон /classes/sklad/layout.price.big.tpl
 * и выбирая товары из таблицы trade_sklad
 * Возвращает сообщение со ссылкой на этот файл
 * @see fprintprice()
 * @return string
 */ 
 
 function printprice() //  
 {
// Шаблон ценника 
 $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/sklad/layout.price.big.tpl');
 $keys = $_POST['tovars']; // multi
 $ui = 'К печати ценников:'.count($keys);
 foreach ($keys as $artikul) {
 $this->components['db']->setTable('trade_sklad');
 $this->components['db']->Select('*', "artikul='$artikul'");
 $data =  $this->components['db']->Read()[0];
 $this->components['view']->SetVar('DESCRIPTION', $data['description']);
 $this->components['view']->SetVar('PRICE', $data['price']);
 $this->components['view']->CreateView(); 
 $t .= $this->components['view']->GetView();
 }
 $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/sklad/layout.printer.tpl');
 $fh = fopen($_SERVER['DOCUMENT_ROOT'].'/prices.html', 'w+');
 $this->components['view']->SetVar('ITEMS', $t);
 $this->components['view']->CreateView();
 fwrite($fh, $this->components['view']->GetView());
 fclose($fh);
 $ui .= "<a href='/prices.html'>Скачать ценники</a>";
 return $ui;
 }
 
 
 /**
 * Форма добавления товара в накладную
 * Заполняется шаблон /classes/sklad/sklad.count.tpl
 * Пользователь указывает количество товара
 * артикул товара передается через $_GET['artikul']
 * @return string
 */ 
  
 function FAddToBill() 
 {
 $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/sklad/sklad.count.tpl');
		$this->components['view']->SetVar('ACTION_NAME', 'Внести товар в накладную');
	  	$this->components['view']->SetVar('ARTIKUL', $_GET['artikul']);
		
		$artikul = $_GET['artikul'];
		
		$this->components['db']->setTable('trade_sklad');
		$this->components['db']->Select(' * ', "artikul='$artikul'");
		$data = $this->components['db']->Read()[0];
		$description = $data['captiontxt'];
	    $this->components['view']->SetVar('DESCRIPTION', $description);

		
		$this->components['view']->SetVar('FORM_ACTION', 'addtobill');
     	$this->components['view']->SetVar('COUNT', '');
		$this->components['view']->SetVar('BUTTON_NAME', 'Добавить товар в накладную');
		$this->components['view']->CreateView();
  	$ui = $this->components['view']->GetView();
	return $ui;
 }

 /**
 * Форма регистрации звонков
 * Заполняется шаблон /classes/sklad/sklad.faddcall.tpl 
 * @return string
 */  
 
function faddcall()
{
    $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/sklad/sklad.faddcall.tpl');
	$this->components['view']->CreateView();
return $this->components['view']->GetView();
}

 /**
 * Добавляет информацию о звонке в таблицу trade_sklad_calls
 * Заполняется шаблон /classes/sklad/sklad.callmsg.tpl
 * и возвращается сообщение пользователю
 * @return string
 */  

function addcall() 
{
$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/sklad/sklad.callmsg.tpl');
$this->components['db']->setTable('trade_sklad_calls');
$this->components['db']->Select('count(*) as idmax', '1=1');
$data = $this->components['db']->Read()[0];
$callid=$data['idmax']+1;
$callfrom = $_POST['callfrom'];
$callto = $_POST['callto'];
$callmsg = $_POST['callmsg'];
$calldt = $_POST['calldt'];
$this->components['db']->Insert('callid, callfrom, callto, callmsg, calldt'    , 
"$callid, '$callfrom', '$callto', '$callmsg', '$calldt'");
$this->components['view']->SetVar('MSGCALL', '<a href=/sklad/viewcalls>Запись о звонке внесена!</a>');
$this->components['view']->CreateView();
return $this->components['view']->GetView();
}

 /**
 * Удаляет звонок из таблицы trade_sklad_calls
 * Ключ на удаляемую запись передается по $_GET['callid']
 * Заполняется шаблон /classes/sklad/sklad.callmsg.tpl
 * и возвращается сообщение пользователю
 * @return string
 */ 

function deletecall() 
{
$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/sklad/sklad.callmsg.tpl');
$callid = $_GET['callid'];
$this->components['db']->setTable('trade_sklad_calls');
$this->components['db']->Delete("callid=$callid");
$this->components['view']->SetVar('MSGCALL', '<a href=/sklad/viewcalls>Звонок удален!</a>');
$this->components['view']->CreateView();
return $this->components['view']->GetView();
}
function viewcalls()
{
$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/sklad/sklad.callitems.tpl');    
$this->components['db']->setTable('trade_sklad_calls');
$this->components['db']->Select('*', '1=1 ORDER BY callid DESC');
$items = '';
$rows = $this->components['db']->Read();
foreach ($rows as $i => $data)
{
    $this->components['view']->CreateView();
    $this->components['view']->SetVar('CALLID', $data['callid']);
    $this->components['view']->SetVar('CALLFROM', $data['callfrom']);
    $this->components['view']->SetVar('CALLTO', $data['callto']);
    $this->components['view']->SetVar('CALLMSG', $data['callmsg']);
    $this->components['view']->SetVar('CALLDT', $data['calldt']);
	$this->components['view']->CreateView();
    $items.=$this->components['view']->GetView();    
}

$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/sklad/sklad.calltable.tpl');    
$this->components['view']->SetVar('ITEMS', $items);
$this->components['view']->CreateView();
return $this->components['view']->GetView();
}
 

 /**
 * Форма проводки накладной
 * Информация о накладной берется из массива $_SESSION['bill']
 * Заполняются шаблон(ы) 
 * /classes/sklad/sklad.bill.items.tpl
 * /classes/sklad/sklad.bill.table.tpl
 * и строится HTML-форма, в которую можно ввести сведения о накладной
 * вместе со списком товаров в этой накладной
 * @return string
 */ 

function FProvodka() // 
 {
// Проводка накладной
$totally = 0.00;
$nomers = 0;
$bill = "";
isset($_SESSION['bill']['count']) ? $bill_count = $_SESSION['bill']['count'] : $bill_count = 0;

if ($bill_count<=0)
 {$cart = 'Корзина пуста';}
else
 {    
   $items = "";
   $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/sklad/sklad.bill.items.tpl');
   for ($i=1; $i<=$bill_count; $i++)
     {
		 $this->components['view']->SetVar('NOMER', $i);	
		 $this->components['view']->SetVar('ARTIKUL', $_SESSION['bill']["item".$i]['artikul']);	 
 		 $this->components['view']->SetVar('COUNT', $_SESSION['bill']["item".$i]['count']);	 
		 // Узнаем информацию о товаре из базы	 
		 $this->components['db']->setTable('trade_sklad');
	$artikul = $_SESSION['bill']["item".$i]['artikul'];
	$this->components['db']->Select(' * ', "artikul='$artikul'");

	$data = $this->components['db']->Read()[0];
	
	   	$this->components['view']->SetVar('ARTIKUL', $data['artikul']);
     	$this->components['view']->SetVar('DESCRIPTION', $data['captiontxt']);
		$this->components['view']->SetVar('TYPE', $data['type']);
		$this->components['view']->SetVar('PRICE', $data['price']);
	
		 		 		 
		 $nomers = $nomers + $_SESSION['bill']["item".$i]['count'];
		 $totally = $totally + $_SESSION['bill']["item".$i]['count'] * $data['price'];
		  		 
	     $this->components['view']->CreateView();
		 $items .= $this->components['view']->GetView();
	 };
   $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/sklad/sklad.bill.table.tpl');	 
   $this->components['view']->SetVar('ITEMS', $items);	
   $this->components['view']->SetVar('NOMERS', $nomers);	 
   $this->components['view']->SetVar('TOTALLY', $totally);
   $this->components['view']->CreateView();
   $bill = $this->components['view']->GetView();
   
	 };
	 
$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/sklad/provodka.editor.tpl');	 
$this->components['view']->SetVar('FORM_ACTION', 'provodka');
$this->components['view']->SetVar('ACTION_NAME', 'Сформированная накладная:');
$this->components['view']->SetVar('BUTTON_NAME', 'Провести накладную...');	 
$this->components['view']->SetVar('DATE', date("r"));	
$this->components['view']->SetVar('BILL', $bill);
$this->components['view']->CreateView();
return $this->components['view']->GetView();
 } 
 
 
  /**
 * Детализация операции (заказа, продажи, поступления товаров)
 * Ссылка на операцию берется из поля  $_GET['operation']
 * Заполняются шаблон(ы) 
 * /classes/sklad/sklad.detalization.items.tpl
 * /classes/sklad/sklad.detalization.table.tpl
 * и строится HTML-форма, в которой можно 
 * посмотреть подробные сведения об операции на основании
 * сведений из таблиц trade_operations и trade_operations_details
 * @return string
 */ 

 
 function OrderDetails() 
 {
// Проводка накладной
$totally = 0.00;
$nomers = 0;
$operation = $_GET['operation']; // Получить номер операции
// Выбираем товары
   $items = "";
   $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/sklad/sklad.detalization.items.tpl');
   $this->components['db']->setTable('trade_operations_details');
   $this->components['db']->Select("*", "operation='$operation'");
   $i = 0;
   $rows = $this->components['db']->Read();
   foreach ($rows as $i=>$data)
     {
	     $i = $i + 1;
		 $this->components['view']->SetVar('NOMER', $i);	
		 $this->components['view']->SetVar('ARTIKUL', $data['artikul']);	 
 		 $this->components['view']->SetVar('COUNT', $data['count']);	 
		 $this->components['view']->SetVar('PRICE', $data['price']);	 
		   
		 $totally = $totally + $data['count'] * $data['price'];
		 
	     $this->components['view']->CreateView();
		 $items .= $this->components['view']->GetView();
	 };
   $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/sklad/sklad.detalization.table.tpl');	 
   $this->components['view']->SetVar('ITEMS', $items);	
   $this->components['view']->SetVar('NOMERS', $i);	 
   $this->components['view']->SetVar('TOTALLY', $totally);
 // Остальные данные берем согласно журнала trade_operation
   $this->components['db']->setTable('trade_operations');   
   $this->components['db']->Select("*", "operation='$operation'");
   
   $data = $this->components['db']->Read()[0];
   
   $this->components['view']->SetVar('DATE', $data['date']); // Дата
   $this->components['view']->SetVar('AGENT', $data['agent']);	// Контрагент
   $arr = array('minus'=>'ПРОДАЖА', 'plus'=>'ЗАВОЗ');
   $this->components['view']->SetVar('DTYPE',
           $this->components['formitems']->SelectItem($arr,  $data['dtype'], 'dtype'));           
   $arr = array(''=>'ОБЫЧНЫЙ', 'home'=>'ДОСТАВКА НА ДОМ', 'shop'=>'ВЫДАЧА ТОВАРА В МАГАЗИНЕ');
   $this->components['view']->SetVar('STATUS',
           $this->components['formitems']->SelectItem($arr,  $data['status'], 'status'));           
   $this->components['view']->SetVar('USERNAME', $data['username']);	 // Отв. лицо
   $this->components['view']->SetVar('OPERATION', $data['operation']); // Номер операции
   
   
   $this->components['view']->CreateView();
   $bill = $this->components['view']->GetView();
  
	 
$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/sklad/detalization.editor.tpl');	 
$this->components['view']->SetVar('FORM_ACTION', 'processdetalization');
$this->components['view']->SetVar('ACTION_NAME', 'Зарегистрированная накладная:');
$this->components['view']->SetVar('BUTTON_NAME', 'Запустить обработчик. Остатки будут обновлены автоматически...');	 
$this->components['view']->SetVar('DATE', date("r"));	
$this->components['view']->SetVar('BILL', $bill);
$this->components['view']->CreateView();
return $this->components['view']->GetView();
 } 
 
 /**
 *  Удаление торговой операции из журнала
 * Ссылка на операцию берется из поля  $_GET['operation']
 * Удаляются связанные записи из trade_operations и trade_operations_details
 * Возвращает HTML-сообщение пользователю
 * @return string
 */ 
 
 function OrderDelete() 
 {
 $operation = $_GET['operation']; // Получить номер операции
 $this->components['db']->setTable('trade_operations');
 $this->components['db']->Delete("operation='$operation'");
 $this->components['db']->setTable('trade_operations_details');
 $this->components['db']->Delete("operation='$operation'");
 
 return "Удаление операции завершено. <a href='/sklad/vieworders/all/1'>Перейти к списку операций?</a>";
 
 }
 
 /**
 * Обработчик детализации
 * @return string
 */ 
 
 function processdetalization()
 {
     return 'Незапрограммированная функция!';
 }
 
 /**
 * Удаление товара
 * Изымает сведения из товара из таблицы trade_sklad
 * Артикул товара берется из поля  $_GET['artikul'
 * Данные из операционных журналов не удаляются
 * Возвращает HTML-сообщение пользователю
 * @return string
 */ 
 
 
 function DelTovar() 
 {
 $artikul = $_GET['artikul'];
 $this->components['db']->setTable('trade_sklad');
 $this->components['db']->Delete("artikul='$artikul'");
  return "Удаление информации о товаре завершено. Товар снят с учёта. <a href='/sklad/view'>Перейти к списку товаров на складе?</a>";
 }
 
  /**
 * Создает карту интернет-магазина
 * Заполняет шаблоны /classes/SiteMapTool/urlitem.tpl
 * и /classes/SiteMapTool/sitemap.tpl
 * ссылками на первую страницу с категориями товаров
 * и полными описаниями товаров
 * на основании записей в таблицах trade_structure и
 * trade_sklad
 * Записывает карту в файл /shop-sitemap.xml
 * Возвращает HTML-сообщение пользователю
 * @return string
 */ 
 
 
  function CreateSitemap() // Создает карту интернет-магазина
 {
	 $sitemap = "";
     $urls = "";
     $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/SiteMapTool/urlitem.tpl');
	 // Для групп товаров
	 $this->components['db']->setTable('trade_structure');
	 $this->components['db']->Select('*', '1=1');
         $rows = $this->components['db']->Read();
	 foreach ($rows as $i => $data)
	 {
 	   $url = $data['category'];
       $this->components['view']->SetVar('URL',  'http://'.$_SERVER['SERVER_NAME'].'/shop/view/category/'.$url.'/1');
       $this->components['view']->SetVar('FREQ', 'weekly');
	   $this->components['view']->CreateView();
	   $urls .= $this->components['view']->GetView();
	 }
	 $this->components['db']->Clear();
	 // Для товаров
	 $this->components['db']->setTable('trade_sklad');
 	 $this->components['db']->Select('artikul', '1=1');
         $rows = $this->components['db']->Read();
	 foreach ($rows as $i => $data)
	 {
 	   $url = $data['artikul'];
       $this->components['view']->SetVar('URL',  'http://'.$_SERVER['SERVER_NAME'].'/shop/buyer/viewitempage/'.$url);
       $this->components['view']->SetVar('FREQ', 'weekly');
	   $this->components['view']->CreateView();
	   $urls .= $this->components['view']->GetView();
	 }
	 $this->components['db']->Done();
     // Пишем карту сайта
    $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/SiteMapTool/sitemap.tpl');
	$this->components['view']->SetVar('URLS', $urls);
	$this->components['view']->CreateView();
	$sitemap = $this->components['view']->GetView();
    $fp = fopen ($_SERVER['DOCUMENT_ROOT'].'/shop-sitemap.xml', "w+");
    fwrite ($fp, $sitemap);
    fclose ($fp);
	return "Список товаров для поисковиков обновлен и сохранен в файл /shop-sitemap.xml ...";
 }
 

 /**
 * Основной обработчик событий
 * Получает действие (экшн), затребованный пользователем
 * из поля $_GET['action']
 * на основании значения этого поля вызывает
 * функции, которые должны вернуть сообщение пользователю
 * Возвращает это сообщение в качестве своего результата
 * @return string
 */ 
 
 function run()
 {
	 
	 $action = $_GET['action'];
	 switch ($action)
	 {
		 case 'fnew' : {return $this->FNew(); break;};
		 case 'new' : {return $this->aNew(); break;};
		 
		 case 'fnewtype' : {return $this->FNewType();break;};
		 case 'newtype' : {return $this->aNewType();break;};
		 
	     case 'fplus' : {return $this->FPlus();break;};
		 case 'plus' : {return $this->Plus();break;};
		 
		 case 'fminus' : {return $this->FMinus();break;};
		 case 'minus' : {return $this->Minus();break;};
		 
		 case 'fedit' : {return $this->FEdit();break;};
		 case 'edit' : {return $this->Edit();break;};
		 
		 case 'fedittype' : {return $this->FEditType();break;};
		 case 'edittype' : {return $this->EditType();break;};
                 case 'deltype' : {return $this->DelType();break;};
		 
		 case 'addtobill' : {return $this->AddToBill();break;};
		 case 'faddtobill' : {return $this->FAddToBill();break;};
		 case 'fprovodka' : {return $this->FProvodka();break;};
		 case 'provodka' : {return $this->Provodka();break;};
		 case 'clearbill' : {return $this->clearbill();break;};
		 	 
		
		 case 'view' : {return $this->View();break;}
		 
		 case 'vieworders' : {return $this->vieworders();break;};
		 
		 case 'orderdetails' : {return $this->orderdetails();break;};
		  
		 case 'orderdelete' : {return $this->orderdelete();break;};
                 
                 case 'processdetalization' : {return $this->processdetalization();break;};
                 
                 case 'viewcalls' : {return $this->viewcalls();break;};
                 
                 case 'faddcall' : {return $this->faddcall();break;};
                 
                 case 'addcall' : {return $this->addcall();break;};
                 
                 case 'deletecall' : {return $this->deletecall();break;};
			  
	     case 'deltovar' : {return $this->deltovar();break;};
		 
		 case 'createsitemap' : {return $this->createsitemap();break;};
		 
		 case 'fprintprice' : {return $this->fprintprice();break;};
		 
		 case 'printprice' : {return $this->printprice();break;};                 
                 	 
		 default : {return $this->Unknown();}
	 }
 }

}

?>