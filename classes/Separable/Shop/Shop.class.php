<?php
if (!defined('APP')) {die('ERROR shop.class.php');};
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core/ClassFactory/ClassFactory.class.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core/DatabaseLayer/DatabaseLayer.class.php';

/**
 * \brief Пополняет список тегов
 * Добавляет теги в список тегов - файл /etc/tags.txt
 * Один и тот же тег не попадает в список дважды
 * @param string $tags 
 */
function savetags($tags)
{
  $filename = $_SERVER['DOCUMENT_ROOT'].'/etc/tags.txt';
  $fh = fopen($filename, "r+");
  $txt = fread($fh, filesize);
  fclose($fh);
  $fh = fopen($_SERVER['DOCUMENT_ROOT'].'/etc/tags.txt', "w+");  
  $tg = explode(",", $tags);
  $newtxt = $txt;
  foreach ($tg as $t)
  {
   if (strpos($txt, $t)<=0)
    {
	   $newtxt .= ", ".$t;
	};
  };
  fwrite($fh, $newtxt);
  fclose($fh);   
}


/**
 * \brief Читает список тегов.
 * Считывает список тегов из файл /etc/tags.txt
 * @return string
 */

function getalltags()
{
  $filename = $_SERVER['DOCUMENT_ROOT'].'/etc/tags.txt';
  $fh = fopen($filename, "r+");
  $txt = fread($fh, filesize);
  fclose($fh);
  return $txt;
}

/**
 * \brief Виджет "теги"
 * Возвращает HTML-разметку со списком ссылок на поиск
 * по тегам
 * @param string $tags - теги, разделенные запятями
 * @return string 
 */
function tagview($tags)
{
$tg = explode(",", $tags);
  $view = "";
  foreach ($tg as $t)
  {
   $t = trim($t);
   $view .= "<a href='/shop/findbytag/".urlencode($t)."'>$t</a> ";
  }
  return $view;
}


/**
 * \brief Виджет "похожие товары"
 * Возвращает HTML-разметку со списком ссылок на похожие товары (поле see_also таблицы SKLAD)
 * по тегам
 * @param string $tags - теги, разделенные запятыми
 * @return string 
 */
function alsoview($tags)
{
$p = null;
if (trim($tags)!=="")
{
$tg = explode(",", $tags);
  $view = "";
  foreach ($tg as $t)
  {
   $t = trim($t);
   $db = new DatabaseLayer($p);
   $db->setTable('trade_sklad');
   $db->Select('*', "artikul='$t'");
   $data = $db->Read();
   $img = $data['photo'];
   $captiontxt = $data['captiontxt'];
   $view .= "<div style='float:left; padding:10; margin:10'><a href='/shop/buyer/viewitempage/$t' title='$captiontxt'><img src='$img'  width='48' height='auto' style='margin:5px'/></a></div>  ";
  };
}
  else
{
$view = 'не указано';
}
  return $view;
}

/** 
 * \brief Витрина магазина.
 * Класс, предоставляющий пользователю каталог товаров
 * с возможностью поиска по тегам, цене, словам,
 * добавления товара в корзинку и отправления
 * предзаказов администраторам магазина.
 */
class Shop
{
/**
 * Подчиненные компоненты.
 * @var mixed 
 */    
var $components;
/**
 * Заголовок страниц.
 * @var string
 */
var $title;
/**
 * Мета-описание страниц.
 * @var string
 */
var $meta;
/**
 * \brief Конструктор.
 * Подключает подчиненные компоненты и устанавливает параметры по умолчанию
 * @param mixed $params - параметры конструктора
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
	$this->components['search'] = $this->components['factory']->createInstance("SearchTool", $params, 'Helpers'); // Подключаем шаблонизатор	
	$this->components['cache'] = $this->components['factory']->createInstance("CacheLayer", $params, 'Core'); // Кэш
        $this->components['usr'] = $this->components['factory']->createInstance("UserAuth", $params, 'Services'); // Кэш
        $this->components['gb'] = $this->components['factory']->createInstance("GlobalBlocksController", $params, 'Controllers'); // Глобальные блоки
        $this->components['log'] = $this->components['factory']->createInstance("Log", $params, 'Core'); // Лог

   	$this->title = ' обработка запроса... ';
	$this->meta = ' обработка запроса... ';
}
/**
 * Деструктор.
 */
function __destruct()
{
 foreach ($this->components as $key => $value)
	  {
		  unset($this->components[$key]);
	  }
	  unset($this->components);
}


function widgets()
{
     $this->user_area();
}

/**
 * \brief Статистика просмотров описаний.
 * Возвращает число просмотров полных описаний товаров
 * @return integer 
 */

function user_area()
{
    		if (($this->components['usr']->UserChecking())==true) {
$this->components['view']->SetVar('USER_AREA', $this->components['usr']->UserProfile());

 }
 else
 { $this->components['view']->SetVar('USER_AREA', $this->components['usr']->UserLogForm()); //  Профиль пользователя
 };
}

function TotalViews()
{
$this->components['db']->setTable('trade_sklad');
$this->components['db']->Select('SUM(visitors) as totalviews', ' 1 = 1 ');
$data = $this->components['db']->Read();
return $data['totalviews'];
}
/**
 * \brief Виджет "Случайный товар".
 * Возвращает HTML разметку, в которую помещается
 * фотография, название, цена товара и кнопка-ссылка Купить
 * @return string
 */
function RandomTovar()
{
if ($this->components['cache']->failed('random_tovar'))
{
// Случайный товар
$this->components['db']->setTable('trade_sklad');
$this->components['db']->Select('*', ' 1 = 1 ');
$arr = array();
$k = 0;
$rows = $this->components['db']->Read(); foreach ($rows as $i=>$data)
{
 $k++;
 $arr[$k] = $data;
};
$k = rand(1, $k);

$artikul = $arr[$k]['artikul'];
$description = $arr[$k]['captiontxt'];
$price = $arr[$k]['price'];
$path = $arr[$k]['photo'];
if ($path == '') {$path ='/images/no-photo.jpg';};
$path = 'http://'.$_SERVER['SERVER_NAME'].$path;
$view = 'Артикул:'.$artikul."<br/>";
$view = 'Наименование:'.$description."<br/>";
$view .= "<a href='/shop/buyer/viewitempage/$artikul'><img src='".$path."' width='120'/></a><br/>";
$view .= 'Цена:'.$price."<br/>";
$view .= '<p><a style="text-decoration:none !important; border-bottom:none" href="/shop/buyer/faddtocart/'.$artikul.'"><img src="/images/cart-but.png" /></a></p>';
$this->components['cache']->save('random_tovar', $view); // Кэшируем значение
}
else
{
$view = $this->components['cache']->get_from_cache('random_tovar');
};
return $view;
}

/**
 * \brief Облако тегов.
 * Возвращает HTML-разметку со списком тегов на основании
 * записей  о тегах к товарам из таблицы trade_sklad
 * @return string
 */

function ListOfTags()
{
  
  $tags = array();
  $this->components['db']->setTable('trade_sklad');
  $this->components['db']->Select('tags', ' 1 = 1 ');
  $rows = $this->components['db']->Read(); foreach ($rows as $i=>$data)
    {
        $tag = $data['tags'];
		$tr = array();
		if (strpos($tag, ',')>0)
		{
        $tr = explode(',' , $tag);
		}
		else if ($tag!='')
		{
		$tr = array(trim($tag));
		};
        foreach ($tr as $t)
          {         
	$tags[trim($t)] = "<a href=http://".$_SERVER['HTTP_HOST'].'/shop/findbytag/'.urlencode(trim($t)).">$t</a> ";
          };
    };
  $html = "";
  foreach ($tags as $t)
    {
      $html .= $t;
    }	
  return $html;
}

/**
 * \brief Запрос на выборку товаров включая вложенные категории.
 * Возвращает подстроку WHERE для данного запроса
 * @return string 
 */

function ChildWhereInclude($category)
{
$zapros = " ( type = '$category' ) ";
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
 * \brief Полное описание товара (под катом)
 * Разрезает $dtext на часть до -- и часть после --.
 * Возвращает в качестве полного описания часть после --.
 * @param string $dtext - текст, в котором краткое и полное описание разделены двумя дефисами --
 * @return string
 */

function full($dtext) 
{
     $cut = strpos($dtext, "--");
		
		if ( $cut > 0)
		{
		return  substr($dtext, $cut+3);
		}
		else
		{
		 return  $dtext;
		};
}
/**
 * \brief Краткое описание товара 
 * Разрезает $dtext на часть до -- и часть после --.
 * Возвращает в качестве краткого описания часть до --.
 * @param string $dtext - текст, в котором краткое и полное описание разделены двумя дефисами --
 * @return string
 */
function short($dtext) 
{
  $cut = strpos($dtext, "--");
		
		if ( $cut > 0)
		{
		return substr($dtext, 0, $cut);
		}
		else
		{return $dtext;		
		};
}


/**
 * \brief  Добавляет товары в операцию со статусом ожидание.
 * Выполняет проводку - регистрирует заказ со статусом ожидание
 * в таблицах trade_operations и trade_operations_details
 * При этом информацию берется из массива $_SESSION['cart'], где
 * $_SESSION['cart']['count'] - число товаров в корзине покупателя
 * $_SESSION['cart']["item".$i] - информация о позиции $i
 * $_SESSION['cart']["item".$i]['artikul'] - артикул товара
 * $_SESSION['cart']["item".$i]['count'] - количество учетных единиц товара
 */
function provodka() 
{
if ($_SESSION['cart']['count']>0)
 { 
   $items = "";
   
   
   
   $operation = 'o'.rand(1,9999)."reg".date("d").date("m").date("y");
  
   for ($i=1; $i<=$_SESSION['cart']['count']; $i++)
     {
		 $atrikul = $_SESSION['cart']["item".$i]['artikul'];	 
 		 $count = $_SESSION['cart']["item".$i]['count'];	 
		 // Узнаем информацию о товаре из базы	 
		 $artikul = $_SESSION['cart']["item".$i]['artikul'];
		 
		 $this->components['db']->setTable('trade_sklad');
		 
	     $this->components['db']->Select('artikul, description, type, price', "artikul='$artikul'");

	$data = $this->components['db']->Read();
	
	  $price =  $data['price'];
		
	$this->components['db']->setTable('trade_operations_details');
	
	$this->components['db']->Insert("operation, artikul, price, count", "'$operation', '$artikul', $price, $count");
		 		 		 
		  		 
	
	 };
  
  $dtype = 'minus';
  $agent = $_POST['agent'];
  $username = $this->getUserNameFromSession();
  $date = $_POST['date'];
  $username = $this->getUserNameFromSession();
  $status = 'ожидание';
  $this->components['db']->setTable('trade_operations');
  $this->components['db']->Insert("operation, date, agent, status, username, dtype",  "'$operation', '$date', '$agent', '$status', '$username', '$dtype'");   
	 };
	 // Сбрасываем корзинку
	$_SESSION['cart']['count'] = 0;	
}

/**
 * Получает имя пользователя на основании ключа $_SESSION['ukey']
 * @return string 
 */
function GetUsernameFromSession()
{
        $this->components['db']->setTable('users');
        if ( isset($_SESSION['ukey'])  )
		  { $ukey = $_SESSION['ukey']; }
		else {$ukey = ""; };
        $username = $this->components['db']->getCell('user', "ukey='$ukey'");
        return $username;
}

 /**
  * \brief Выводит хлебные крошки в навигации.
  * Выводит HTML - строку со ссылками, содержащими путь от
  * корневой категории до категории $aCategory
  * @param string $aCategory - текущая категория
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
   
    $h = " >  <a href=/shop/view/category/$Category/1>$Cat_name</a> ".$h;
	if ($Parent == '') break;
    if ($Parent == 'root') break;
	$Category = $Parent;
	$num_iter--;
	 };
	 $h = '<a href=/shop/view/>Общий список товаров</a>&nbsp;'.$h;
	 if ($h!='') return $h;
	}
 /**
  * \brief Выводит список категорий.
  * Выводит HTML - блок со ссылками на текущий раздел и его подразделы
  * на основании значения поля $_POST['category'] и записей в таблице trade_structure
  * @return string 
  */
function Categories() 
{
$items = "";
$this->components['db']->setTable('trade_structure');
( isset( $_GET['category'] ) ) ? $category = $_GET['category'] : $category = "";
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
		( isset( $_GET['category'] ) ) ? $mycat = $_GET['category'] : $mycat = "";
		if ($category!=$mycat)
		{
		$items .= "<a href='/shop/view/category/$category/1'>$catname</a><br/>";
		}
		else
		{
		$items .= "<b>$catname</b><br/>";
		};
	};
if ($items=='') {$items = 'Вложенных разделов нет';};
return $items;
}

/**
  * \brief Выводит сообщение о недоступном действии.
  * Выводит HTML - сообщение, что заданное пользователем
  * действие не соответствует ни одному из настроенных на сайте
  * @return string 
  */
function Unknown()
{
	return "ОШИБКА! Неизвестное действие... <a href='/shop/view'>К витрине</a>";
}

/**
  * \brief Возвращает ассоциативный массив со списком имен категорий
  * Выполняет запрос к таблице trade_structure, чтобы вернуть
  * массив, в котором ключом является ID категории, а значением
  * название этой категории
  * @return array[string] of string
  */
function GetNamesOfCategories()
{
    $this->components['db']->setTable('trade_structure');
    $names = array();
    $this->components['db']->Select(' * ', '1 = 1');
    $rows = $this->components['db']->Read(); foreach ($rows as $i=>$data)
    {
        $names[$data['category']] = $data['catname'];
    };
    return $names;
}
/**
 * \brief Виджет "МиниКорзина покупателя"
 * Возвращает в формате HTML краткий список
 * покупок клиента - название, цену и количество товара,
 * картинку и тип товара, а также общую сумму заказа и
 * число заказанных товаров
 * @return string
 */
function MiniCart()
{
// Предзаказ
$totally = 0.00;
$nomers = 0;
( isset($_SESSION['cart']['count'] ) ) ? $cc = $_SESSION['cart']['count'] : $cc = 0;

if ( $cc <=0 )
 {$cart = 'Корзина пуста';}
else
 { 
   $items = "";
   $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/Separable/shop/shop.minicart.items.tpl');
   $names = $this->GetNamesOfCategories();
   for ($i=1; $i<=$cc; $i++)
     {
		 $this->components['view']->SetVar('NOMER', $i);	
		 $this->components['view']->SetVar('ARTIKUL', $_SESSION['cart']["item".$i]['artikul']);	 
 		 $this->components['view']->SetVar('COUNT', $_SESSION['cart']["item".$i]['count']);	 
		 // Узнаем информацию о товаре из базы	 
		 $this->components['db']->setTable('trade_sklad');
	$artikul = $_SESSION['cart']["item".$i]['artikul'];
  
  
  
	$this->components['db']->Select('*', "artikul='$artikul'");
 
	$data = $this->components['db']->Read()[0];
	//print_r($data);
  
	   	$this->components['view']->SetVar('ARTIKUL', $data['artikul']);
     	$this->components['view']->SetVar('DESCRIPTION', $data['captiontxt']);
		$this->components['view']->SetVar('TYPE', $names[$data['type']]);
		$this->components['view']->SetVar('PRICE', $data['price']);
		
			if ($data['photo']!="")  
		{$this->components['view']->SetVar('PATH', $data['photo']); }
		else {$this->components['view']->SetVar('PATH', '/images/no-photo.jpg');};
	
		 		 		 
		 $nomers = $nomers + $_SESSION['cart']["item".$i]['count'];
		 $totally = $totally + $_SESSION['cart']["item".$i]['count'] * $data['price'];
		  		 
	     $this->components['view']->CreateView();
		 $items .= $this->components['view']->GetView();
	 };
   $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/Separable/shop/shop.minicart.table.tpl');	 
   $this->components['view']->SetVar('ITEMS', $items);	
   $this->components['view']->SetVar('NOMERS', $nomers);	 
   $this->components['view']->SetVar('TOTALLY', $totally);	
   $this->components['view']->SetVar('COUNT', $_SESSION['cart']['count']);	
   $this->components['view']->CreateView();
   $cart = $this->components['view']->GetView();
   
	 };
	 
$this->components['view']->CreateView();
return $this->components['view']->GetView();
  
}

/**
 * \brief Число позиций (различных артикулов) в корзинке покупателя.
 * На основании значения $_SESSION['cart']['count'] предоставляет
 * число позиции в товарной накладной на отпуск покупателю
 * @return int 
 */
function ItemsInCart()
{
    ( isset($_SESSION['cart']['count'] ) ) ? $cc = $_SESSION['cart']['count'] : $cc = 0;
    return $cc;
}
/**
 * \brief Добавлен ли товар в корзину?
 * Если запись о товаре есть в каком-то $_SESSION['cart']["item".$i]['artikul'],
 * возвращает true.
 * @param string $artikul - артикул изделия
 * @return boolean 
 */
function isItemInCart($artikul)
{
   // 
    $cc = $this->ItemsInCart();
    $ok = false;
    for ($i=1; $i<=$cc; $i++)
    {
        if ($_SESSION['cart']["item".$i]['artikul'] == $artikul) {$ok = true;};
    };
    return $ok;
}

/**
 * \brief Виджет "Витрина".
 * Возвращает список товаров в несколько колонок и постраничный переключатель.
 * Данные берутся из полей $_GET['category'] и $_GET['page']
 * Если роль пользователя удалось определить как seller (продавец),
 * предоставляются дополнительные ссылки на редактирование товара, определенные
 * в модуле Склад.
 * @return string 
 */
function View()
{

        $user = $this->GetUsernameFromSession();
		$this->components['db']->setTable("users");
		$this->components['db']->Select("role", "user='$user'");
		$data = $this->components['db']->Read();
		$role = $data['role'];
    
    (isset($_GET['category']) ) ?  $category = $_GET['category'] : $category = "";
	$breadcrumbs = $this->breadcrumbs($category);
	$searchform = $this->components['search']->SearchForm("/shop/", "Поиск товара");
	
	
	$where = urldecode($category);
	if ($where == "") {$where = "1=1";} else {$where = $this->ChildWhereInclude($category);};
	
	$minicart = "Корзина";
	$minicart = $this->MiniCart();	 
   if ($minicart == "") {$minicart = "<div style='color:#FF0000'>Корзина пуста</div>";};
	$this->components['view']->SetVar('MINICART', $minicart);
         
$this->components['view']->SetVar('html_vkontanke_w', 'test...');	

	$items = '';
	$articles = $this->components['options']->GetOption('SHOP_ITEMSPERPAGE');
	if ($category=="")
	{
	$link = "/shop/view/";
	}
	else
	{
	$link = "/shop/view/category/$category/";
	};
	$max_col = $this->components['options']->GetOption('SHOP_MAXCOL'); 
	$page = $_GET['page'];
	if ($page<=0) {$page=1;};
	$from_page = $articles * ($page - 1);
	
	// Для поисковика
	if ($category == "")
	
	{$this->title = 'Общий список товаров';}
	else
	{
    $this->components['db']->setTable('trade_structure');
	$this->components['db']->Select('catname', "category='$category'");	
	$data = $this->components['db']->Read();
	$this->title = " товары '".$data['catname']."' cтр. ".$page;
	};	
	$this->meta = $this->title.' купить в Оренбурге';
	
	$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/Separable/Shop/shop.items.tpl');
    $this->components['db']->setTable('trade_sklad');
	
	$this->components['db']->Select('*', " $where");
	
	$tcount = $this->components['db']->NRows();
	
	$this->components['db']->Select('*', " $where ORDER BY artikul, price LIMIT $from_page, $articles");
	
  
	
	if (mysqli_num_rows($this->components['db']->sql_result)!=0) 
	{
		$c = 1;
	$rows = $this->components['db']->Read(); foreach ($rows as $i=>$data)
	{
		if ($c==1) {$items .= "<tr>";};
	   	$this->components['view']->SetVar('ARTIKUL', $data['artikul']);
		$artikul = $data['artikul'];
		$this->components['view']->SetVar('TYPE', $data['type']);
		if ($data['photo']!="")  
		{$this->components['view']->SetVar('PATH', $data['photo']); }
		else {$this->components['view']->SetVar('PATH', '/images/no-photo.jpg');};
		
		
		
     	$this->components['view']->SetVar('DESCRIPTION', $data['captiontxt']);
		$this->components['view']->SetVar('UNDERCUT', $data['description']);
		
		// Служебные ссылки
		$this->components['view']->SetVar('SPEC_LINKS', '');
		if ($role=="seller")
		{
			$this->components['view']->SetVar('SPEC_LINKS', "<a href=/sklad/item/fedit/$artikul>Правка</a> <a href=/sklad/item/faddtobill/$artikul>В накладную</a>");                       
                        
		}; // end if
		
     	$this->components['view']->SetVar('PRICE', $data['price']);
        $old_price = ceil($data['price']*1.15); // 15% наценка
        $old_price = ceil($old_price / 100) * 100;
	$this->components['view']->SetVar('OLD_PRICE',  $old_price);
		if ($data['count']>0)
		{
			$this->components['view']->SetVar('EXISTS', 'Есть в наличии');
		}
		else
		{
			$this->components['view']->SetVar('EXISTS', 'Под заказ');
		};  // end if
                if ($this->isItemInCart($data['artikul'])==true)
                {
                    $this->components['view']->SetVar('ITEMINCART', 'background-color:#FFCC66');
                    $this->components['view']->SetVar('CONTEXT', 'Включен в заказ');
                }
                else
                {
                    $this->components['view']->SetVar('ITEMINCART', '');
                    $this->components['view']->SetVar('CONTEXT', '');
                };  // end if
                
		$this->components['view']->SetVar('BREADCRUMBS', $breadcrumbs);
		$this->components['view']->CreateView();
		$items .= $this->components['view']->GetView();
		$c = $c + 1;
		if ($c > $max_col) {$c = 1; $items .= "</tr>";};
	}  // end while

      $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/Separable/Shop/shop.table.tpl');
	
	
	 // ПАГИНАЦИЯ
	 
 $this->components['paginator']->SPages($this, $page, $where, $category, $articles, $link, "trade_sklad");
 
  
	
	 $this->components['view']->SetVar('TOTAL', "Товаров в этой группе: $tcount | Страница ");
	 
	$this->components['view']->SetVar('ITEMS', $items); 
	
	$this->components['view']->SetVar('LISTOFTAGS', $this->ListOfTags());
	
	$this->components['view']->SetVar('RANDOM_TOVAR', $this->RandomTovar());
	
	$this->components['view']->SetVar('PRICEFINDER', $this->PriceFinder());
	
		
	$categories = $this->Categories();
	
	$this->components['view']->SetVar('CATEGORIES', $categories); 

	$this->components['view']->SetVar('SEARCHFORM', $searchform); 
        
        
	
	$this->components['view']->SetVar('TOTALVIEWS', $this->TotalViews());	
	
        $this->widgets();
	
	$this->components['view']->CreateView();
	$ui = $this->components['view']->GetView();
	return $ui;
	} else {$ui = "Товаров в этом разделе на складе нет!"; return $ui; };
	
	
	
}

/**
 * \brief Страница с полным описанием изделия.
 * На основании $_GET['artikul'] делает запрос к таблице trade_sklad
 * и заполняет шаблон /classes/Separable/Shop/shop.item.view.tpl и возвращает
 * HTML разметку 
 * @return string
 */

function ViewItemPage()
{
    
$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/Separable/Shop/shop.item.view.tpl');
$this->components['db']->setTable("trade_sklad");
$artikul = $_GET['artikul'];
$this->components['db']->Select("*", "artikul='$artikul'");
$data = $this->components['db']->Read()[0];
// Статистика просмотров		
	$v = $data['visitors'] + 1; 
	
	$this->components['view']->SetVar('VISITORS', $v);

	// Для поисковика
	
	$this->title = $data['captiontxt'];
	$this->meta = $this->title.' купить в Оренбурге';
	

		$this->components['view']->SetVar('TYPE', $data['type']);
		if ($data['photo']!="")  
		{$this->components['view']->SetVar('PATH', $data['photo']); }
		else {$this->components['view']->SetVar('PATH', '/images/no-photo.jpg');};
		
		
		
     	$this->components['view']->SetVar('DESCRIPTION', $data['captiontxt']);
		$stubby = "Возникли вопросы? Звоните 93-66-43";
		
		if  ($data['description']=='')
		{
		$this->components['view']->SetVar('UNDERCUT', $stubby);
		}
		else
		{
		$this->components['view']->SetVar('UNDERCUT', $data['description']);
		};
		
		
     	$this->components['view']->SetVar('PRICE', $data['price']);
		if ($data['count']>0)
		{
			$this->components['view']->SetVar('EXISTS', 'Есть в наличии');
		}
		else
		{
			$this->components['view']->SetVar('EXISTS', 'Под заказ');
		};
			$this->components['view']->SetVar('ARTIKUL', $data['artikul']);
			// Обработать теги!
			$this->components['view']->SetVar('TAGS',  tagview($data['tags']));
// Похожие товары!
			$this->components['view']->SetVar('SEE_ALSO',  alsoview($data['see_also']));
 // Путь в товару			
	    $breadcrumbs = $this->breadcrumbs($data['type']);			
		
		$this->components['view']->SetVar('BREADCRUMBS', $breadcrumbs );
		
		
		
		
		$user = $this->GetUsernameFromSession();
		$this->components['db']->setTable("users");
		$this->components['db']->Select("role", "user='$user'");
		$data = $this->components['db']->Read();
		$this->components['view']->SetVar('SPEC_LINKS', '');
		if ($data['role']=='seller')
		{
		$this->components['view']->SetVar('SPEC_LINKS', "<a href=/sklad/item/fedit/$artikul>правка</a>");
		};
		
		
		$this->components['db']->setTable('trade_sklad');
		$this->components['db']->Update("visitors=$v", "artikul='$artikul'");
		
		
		$this->components['view']->CreateView();
		return $this->components['view']->GetView();
}

/**
 * \brief Полный прайс-лист магазина
 * Вывод в формате - название товара, цена (со ссылкой)
 * @return string 
 */

function ViewPrice()
{
$this->components['db']->setTable('trade_sklad');
$this->components['db']->Select('*', " 1 = 1 ORDER BY captiontxt, price");
$view = "<h1>Прайс-лист</h1>
<table class='table table-striped' width='100%'><tr><th>Название</th><th>Цена</th></tr>";
$i = 0;
	$rows = $this->components['db']->Read(); foreach ($rows as $i=>$data)
		{
		$a = $data['artikul'];
//		$style='';
// if (($i % 2)==0) {$style='background-color:#ffffff';} else {$style='background-color:#e8e8e8';};
$i = $i + 1;
		$view .= "<tr  style='$style'><td><a href='/shop/buyer/viewitempage/$a'>" . $data['captiontxt'] . "</a></td><td>" . $data['price'] . "</td></tr>";
		};
$view .= "</table>";		
return $view;
}

// ----------------- ЛОГИКА

function in_cart_item($i){
  return $_SESSION['cart']["item".$i]["artikul"];
}

function reg_in_session($artikul, $count){
  $_SESSION['cart']['count'] = $_SESSION['cart']['count'] + 1; // Увеличить число товаров в корзине
  $nomer = $_SESSION['cart']['count'];
	$_SESSION['cart']["item".$nomer]['artikul'] = $artikul;
	$_SESSION['cart']["item".$nomer]['count'] = $count;
}

function inc_in_session($found, $count){
    // Увеличить на $_POST['count'] запись о товаре в $_SESSION['cart']["item".$found]['count']
 $_SESSION['cart']["item".$found]['count'] = $_SESSION['cart']["item".$found]['count'] + $count;
}

function debug_session_info(){
  $str = "";
  $count = $_SESSION['cart']['count'];
  for($i=1;$i<=$count;$i++){
     $artikul = $_SESSION['cart']['item'.$i]['artikul'];
     $count = $_SESSION['cart']['item'.$i]['count'];
     $str = $str . " $i $artikul $count \n\r";
  };
  file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/sessionState.log', $str);
}

/**
 * \brief Вносит товар в корзинку покупателя и уведомляет об этом
 * Добавляет запись о товаре в массив $_SESSION['cart']["item".$i]["artikul"]
 * @return string 
 */
function addtocart()
{  



$artikul = $_POST['artikul'];
$item_count  = isInt($_POST['count']);
$items_in_cart = $_SESSION['cart']['count'];
if ($_POST['count'] !== false)
{
 // Поиск по корзине
 $found = 0;
 for ($i=1; $i<=$items_in_cart ; $i++)
 {
 if ($artikul == $this->in_cart_item($i))
   {
     $found = $i;
	 break;
   };
 };
 
 if ($found==0)
 {
	$this->reg_in_session($artikul, $item_count);
  $this->debug_session_info();
}
else
 {
  $this->inc_in_session($found, $item_count);
  $this->debug_session_info();
 }
	return "Товар $artikul добавлен. Перейти к <a href='/shop/buyer/fpredzakaz'>оформлению предзаказа</a> или <a href='/shop/view'>продолжить покупки?</a>";		
}
else
{
      
        return "Опечатка в количестве товара...<a href='/shop/buyer/faddtocart/$artikul'>попробуйте снова</a>";		    
};



}
 
/**
 * \brief Очищает корзинку.
 * Удаляет сведения о заказе путем обнуления $_SESSION['cart']['count']
 * @return string 
 */
function clearcart()
{
	$_SESSION['cart']['count'] = 0;
   return "Корзина очищена! <a href='/shop/view'>Выбрать другие товары?</a>";		
}  
  

// ----------------- ФОРМЫ

/**
 * \brief Форма предзаказа.
 * Возвращает HTML разметку формы предзаказа, заполняя шаблоны
 * /classes/Separable/Shop/shop.cart.items.tpl и
 * /classes/Separable/Shop/shop.cart.table.tpl
 * значениями, согласно записям в $_SESSION['cart']
 * @return string
 */
 function FPredzakaz()
 {
// Предзаказ
$totally = 0.00;
$nomers = 0;
if ($_SESSION['cart']['count']<=0)
 {$cart = 'Корзина пуста';}
else

 { 
   $items = "";
   $names = $this->GetNamesOfCategories();
   $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/Separable/Shop/shop.cart.items.tpl');
   for ($i=1; $i<=$_SESSION['cart']['count']; $i++)
     {
		 $this->components['view']->SetVar('NOMER', $i);	
		 $this->components['view']->SetVar('ARTIKUL', $_SESSION['cart']["item".$i]['artikul']);	 
 		 $this->components['view']->SetVar('COUNT', $_SESSION['cart']["item".$i]['count']);	 
		 // Узнаем информацию о товаре из базы	 
		 $this->components['db']->setTable('trade_sklad');
	$artikul = $_SESSION['cart']["item".$i]['artikul'];
	$this->components['db']->Select(' * ', "artikul='$artikul'");

	$data = $this->components['db']->Read();
	
	   	$this->components['view']->SetVar('ARTIKUL', $data['artikul']);
     	$this->components['view']->SetVar('DESCRIPTION', $data['captiontxt']);
		$this->components['view']->SetVar('TYPE', $names[$data['type']]);
		$this->components['view']->SetVar('PRICE', $data['price']);
		$this->components['view']->SetVar('PHOTO', $data['photo']);
	
		 		 		 
		 $nomers = $nomers + $_SESSION['cart']["item".$i]['count'];
		 $totally = $totally + $_SESSION['cart']["item".$i]['count'] * $data['price'];
		  		 
	     $this->components['view']->CreateView();
		 $items .= $this->components['view']->GetView();
	 };
   $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/Separable/Shop/shop.cart.table.tpl');	 
   $this->components['view']->SetVar('ITEMS', $items);	
   $this->components['view']->SetVar('NOMERS', $nomers);	 
   $this->components['view']->SetVar('TOTALLY', $totally);	
   $this->components['view']->SetVar('DATE', date("d.m.y"));
   $this->components['view']->CreateView();
   $cart = $this->components['view']->GetView();
   
	 };
	 
$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/Separable/Shop/shop.predzakaz.editor.tpl');	 
$this->components['view']->SetVar('FORM_ACTION', 'predzakaz');
$this->components['view']->SetVar('ACTION_NAME', 'Оформление предзаказа');
$this->components['view']->SetVar('BUTTON_NAME', 'Отправить предзаказ в магазин');	 
$this->components['view']->SetVar('CART', $cart);
$this->components['view']->CreateView();
return $this->components['view']->GetView();
 } 
 
  /**
  * \brief Удаляет из корзинки запись
  * Удаляет из $_SESSION запись о товаре
  * @param int $nomer - номер в массиве $_SESSION['cart']['item'.$nomer]
  * @return string 
  */
 function DelItemFromCart($nomer)
 {
    for ($i=$nomer; $i<=($_SESSION['cart']['count']-1); $i++)
    {
        $n = $i+1;
        $_SESSION['cart']['item'.$i] = $_SESSION['cart']['item'.$n];        
    };
   $_SESSION['cart']['count'] = $_SESSION['cart']['count'] - 1;
 }
 
 /**
  * \brief Пересчитывает корзинку
  * Возвращает ссылку на пересчитанный заказ
  * @return string 
  */
 function Recalc()
 {
     
     // ФИЛЬТРУЕМ
     $err = false;
     for ($i=1; $i<=$_SESSION['cart']['count']; $i++)
     {		 
         $_POST["item$i"] = IsInt($_POST["item$i"]);
         if ($_POST["item$i"] === false) {$err = true; break;};
     };
     if ($err == false)
     {
     // ОБРАБОТЧИК
  for ($i=1; $i<=$_SESSION['cart']['count']; $i++)
     {		 
 		
      $c = $_POST["item$i"];
      
      if (($c>=0) && ($c<=100)) 
                                {
          $_SESSION['cart']["item".$i]['count'] = $c;                  
                                };                                
      if ($c == 0) {$this->DelItemFromCart($i);};
      }; //end for
  return "<a href='/shop/buyer/fpredzakaz'>Предзаказ пересчитан</a>";
     }
     else
     {
       return "<a href='/shop/buyer/fpredzakaz'>Предзаказ не был пересчитан, количество товара указано с опечаткой</a>";
     }
 }
 
/**
 * \brief Форма предзаказа.
 * На основании $_SESSION['cart'] и шаблонов
 * /classes/Separable/Shop/shop.predzakaz.notice.items.tpl
 * и 
 * /classes/Separable/Shop/shop.predzakaz.notice.table.tpl
 * подготавливает уведомление о заказе для администраторов магазина
 * @return string
 */
  function NoticePredzakaz()
 {
$totally = 0.00;
$nomers = 0;
if ($_SESSION['cart']['count']<=0)
 {$cart = 'Корзина пуста';}
else

 { 
   $items = "";
   $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/Separable/Shop/shop.predzakaz.notice.items.tpl');
   for ($i=1; $i<=$_SESSION['cart']['count']; $i++)
     {
		 $this->components['view']->SetVar('NOMER', $i);	
		 $this->components['view']->SetVar('ARTIKUL', $_SESSION['cart']["item".$i]['artikul']);	 
 		 $this->components['view']->SetVar('COUNT', $_SESSION['cart']["item".$i]['count']);	 
		 // Узнаем информацию о товаре из базы	 
		 $this->components['db']->setTable('trade_sklad');
	$artikul = $_SESSION['cart']["item".$i]['artikul'];
	$this->components['db']->Select(' * ', "artikul='$artikul'");

	$data = $this->components['db']->Read();
	
	   	$this->components['view']->SetVar('ARTIKUL', $data['artikul']);
     	$this->components['view']->SetVar('DESCRIPTION', $data['captiontxt']);
		$this->components['view']->SetVar('TYPE', $data['type']);
		$this->components['view']->SetVar('PRICE', $data['price']);
	
		 		 		 
		 $nomers = $nomers + $_SESSION['cart']["item".$i]['count'];
		 $totally = $totally + $_SESSION['cart']["item".$i]['count'] * $data['price'];
		  		 
	     $this->components['view']->CreateView();
		 $items .= $this->components['view']->GetView();
	 };
   $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/Separable/Shop/shop.predzakaz.notice.table.tpl');	 
   $this->components['view']->SetVar('ITEMS', $items);	
   $this->components['view']->SetVar('NOMERS', $nomers);	 
   $this->components['view']->SetVar('TOTALLY', $totally);	
   $this->components['view']->SetVar('COUNT', $_SESSION['cart']["item".$i]['count']);	
   $this->components['view']->CreateView();
   $cart = $this->components['view']->GetView();
   
	 };
	 
$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/Separable/Shop/shop.predzakaz.notice.tpl');	 
$this->components['view']->SetVar('FORM_ACTION', 'predzakaz');
$this->components['view']->SetVar('CART', $cart);
$this->components['view']->SetVar('AGENT', $_POST['agent']);
$this->components['view']->SetVar('DATE', $_POST['date']);
$this->components['view']->CreateView();
return $this->components['view']->GetView();
 } 
  
/**
 * \brief Форма добавления товара в корзинку.
 * На основании $_GET['artikul'], заполняет шаблон
 * /classes/Separable/Shop/shop.cart.add.tpl
 * с тем, чтобы пользователь мог задать количество единиц товара.
 * Возвращает в качестве результата HTML форму
 * @return string
 */ 
 function FAddToCart()
 {
$names = $this->GetNamesOfCategories();
     
$this->components['db']->setTable('trade_sklad');
	$artikul = $_GET['artikul'];
	$this->components['db']->Select('*', "artikul='$artikul'");
	$this->components['view']->SetVar('FORM_ACTION', 'edit');
	$rows = $this->components['db']->Read(); foreach ($rows as $i=>$data)
	{
	   	$this->components['view']->SetVar('ARTIKUL', $data['artikul']);
		$this->components['view']->SetVar('PHOTO', $data['photo']);
     	$this->components['view']->SetVar('DESCRIPTION', $data['captiontxt']);
		$this->components['view']->SetVar('TYPE', $names[$data['type']]);
	}	 
// В корзину
$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/Separable/Shop/shop.cart.add.tpl');	 
$this->components['view']->SetVar('FORM_ACTION', 'addtocart');
$this->components['view']->SetVar('ACTION_NAME', 'Пожалуйста, укажите количество товара');
$this->components['view']->SetVar('BUTTON_NAME', 'Положить товар в корзину');
$this->components['view']->CreateView();
return $this->components['view']->GetView();
 } 
 
 /**
  * \brief Проводка предзаказа с уведомлением по email администратора.
  * Уведомляет администратора о заказе и запускает обработчик проводки
  * Возвращает HTML-сообщение
  * @see provodka()
  * @return string 
  */
 function predzakaz()
 {
	$to =  $this->components['options']->getOption('EMAIL_ADMIN');
	$subject = 'Предзаказ от '.$username;
	$from = $to;
	$headers =  "From: $from\r\n";
	$headers .= "Content-type: text/plain; charset=UTF-8\r\n";
	mail($to, $subject, $this->NoticePredzakaz(), $headers); // Уведомляем администратора
	$this->provodka(); // Проводим документ по базе
    return "Предзаказ зарегистрирован. С вами свяжется продавец и уточнит детали. <a href='/shop/view'>Ничего не забыли?</a>";
 }
 
 /**
  * \brief Поиск по произвольному слову в кратком и(или) полном описании.
  * Настраивает компонент Scorpio на запрос, выполняет его и возвращает
  * выдачу результатов поиска в HTML формате
  * @return string
  */
 function Search()
 {

  $this->components['search']->SearchInTable('trade_sklad')->OutToTpl('/classes/Separable/Shop/shop.search.items.tpl')->OfColsCount(3);
  $this->components['search']->UsingKeyForTitle('captiontxt')->UsingKeyForSearch( array('description', 'captiontxt'));
  
  $this->components['search']->ViewOptions(
                    array('artikul'=>'ARTIKUL', // переменные шаблона
                          'captiontxt'=>'DESCRIPTION',
                          'photo'=>'PATH',
                          'price'=>'PRICE')
					 )->PathToItemBy('artikul'); 
 $this->components['search']->UseSearchUrl('http://'.$_SERVER['SERVER_NAME'].'/shop/search')->UseItemUrl('/shop/buyer/viewitempage/');
 $this->components['search']->KeyForPagenum('pagenum');
 $this->components['search']->KeyForSearch('searchtext'); 
 
 $result = $this->components['search']->ItemsPerPage(12)->SearchResult();
 return $result;
 }
 
 /**
  * \brief Выполняет поиск по меткам
  * Ищет по меткам товары в таблице trade_sklad на основании
  * ввода из поля $_GET['tags'] и возвращает выдачу
  * в качестве своего результата
  * @return string
  */
 function SearchByTag()
 {
 $this->title = 'Подбор по меткам ';
 $items_ui = $this->components['options']->GetOption('SHOP_ITEMSTAGSEARCH'); // 12;
 // Число колонок 
 $col_count = $this->components['options']->GetOption('SHOP_COLSSTAGSEARCH'); // 4;
 $width = round(100 / $col_count);
 // Параметр-тег
 $tag = trim(urldecode($_GET['tag']));
 $is_paged = strpos($tag, '/');
 $page_nom = 1;
 if ($is_paged !== false)
 {
 $arr = explode('/', $tag);
 $page_nom = $arr[1];
 $tag = $arr[0]; 
 }
 $this->components['db']->setTable('trade_sklad');
 $this->components['db']->Select('COUNT(*) as items_total', " (tags LIKE '%$tag%')  "); // по тегу
 $data = $this->components['db']->Read();
 $items_total = $data['items_total'];
 $pages_total = ceil( $items_total / $items_ui);
 $this->components['db']->setTable('trade_sklad');
 $limit_nom = ($page_nom - 1) * $items_ui;
 $this->components['db']->Select('*', " (tags LIKE '%$tag%') ORDER BY artikul LIMIT $limit_nom, $items_ui"); // по тегу
 $result = "<table border='0' width='960'>";
 $c = 0;
 $rows = $this->components['db']->Read(); foreach ($rows as $i=>$data)
 {
  if (($c == 0)  or (($c % $col_count)== 0))
    {  $result .= '<tr>'; };
  if ((strpos($data['tags'], $tag) !== false) or ($data['tags']==$tag)) 
  {
    $result .= "<td valign='top' width='$width'>";
	
	if ($data['photo']!="")  
		{$image_path = 'http://'.$_SERVER['SERVER_NAME'].$data['photo']; }
		else 
		{$image_path = 'http://'.$_SERVER['SERVER_NAME'].'/images/no-photo.jpg';};	
    $c = $c + 1;
	$image = "<img src='$image_path' width='120' style='border:0; text-decoration:none'/><br/>";
    $result .= "<a href='/shop/buyer/viewitempage/".$data['artikul']."'>".$image.$data['captiontxt']."</a><br/>".$data['price']."<br/>";
	$result .= "</td>";
  };
   if (($c == 0)  or (($c % $col_count)== 0))
    {  $result .= '</tr>'; };
 };
 $result .= "</table>";
 $paginator = "";
 for ($i = $page_nom-2; $i<=$page_nom+2;$i++)
 {
 if ( ($i>=1) and ($i<=$pages_total)  )
   {
   if ($i != $page_nom)

	{$paginator .= "&nbsp;<a class='pagelink' href='/shop/findbytag/$tag/$i'>$i</a>&nbsp;"; }
	else
	{ $paginator .= "&nbsp; $i &nbsp;";};
   }
 };
 $paginator = "Страница $page_nom из $pages_total ".$paginator;
 if ($c==0) {$c = "0"; $result = ''; $paginator = "";};
 $result = "Искали по тегу $tag и нашли товаров: $items_total<br/>Найдено: $result<br/> $paginator";
 return $result;
 }
 
 /**
  * \brief Выполняет поиск по ценовому диапазону
  * Ищет по цеге товары в таблице trade_sklad на основании
  * ввода из полей $_GET['priceLQ'] и $_GET['priceMQ'] и возвращает выдачу
  * в качестве своего результата
  * @return string
  */
  function SearchByPrice()
 {
 $this->title = 'Подбор по цене';
 $items_ui = $this->components['options']->GetOption('SHOP_ITEMSPRICESEARCH'); // 12
 // Число колонок 
 $col_count = $this->components['options']->GetOption('SHOP_COLSPRICESEARCH'); // 4
 $width = round(100 / $col_count);
 // Параметр-тег
 if (isset($_GET['priceMQ']))
  {
 $priceMQ = trim($_GET['priceMQ']);
  }
  else {$priceMQ  = $_POST['priceMQ']; };
  if (isset($_GET['priceLQ']))
  {
 $priceLQ = trim($_GET['priceLQ']);
 }
  else
  {
  $priceLQ = $_POST['priceLQ'];
  };
 if (isset($_GET['page_nom']))
 {
 $page_nom = trim($_GET['page_nom']);
 }
 else
 {
 $page_nom = 1;
 };
 // ФИЛЬТРАЦИЯ
 $priceLQ = IsInt($priceLQ);
 $priceMQ = IsInt($priceMQ);
 if ( ($priceLQ !== false) && ($priceMQ !== false) )
 {
 $this->components['db']->setTable('trade_sklad');
 $this->components['db']->Select('COUNT(*) as items_total', " ((price >= $priceMQ) AND (price<=$priceLQ))  "); // по тегу
 $data = $this->components['db']->Read();
 $items_total = $data['items_total'];
 $pages_total = ceil( $items_total / $items_ui);
 $this->components['db']->setTable('trade_sklad');
 $limit_nom = ($page_nom - 1) * $items_ui;
 $this->components['db']->Select('*', " ((price >= $priceMQ) AND (price<=$priceLQ)) ORDER BY price LIMIT $limit_nom, $items_ui"); // по тегу
 $result = "<table border='0' width='100%'>";
 $c = 0;
 $rows = $this->components['db']->Read(); foreach ($rows as $i=>$data)
 {
  if (($c == 0)  or (($c % $col_count)== 0))
    {  $result .= '<tr>'; };
  
    $result .= "<td valign='top' width='$width'>";
	
	if ($data['photo']!="")  
		{$image_path = 'http://'.$_SERVER['SERVER_NAME'].$data['photo']; }
		else 
		{$image_path = 'http://'.$_SERVER['SERVER_NAME'].'/images/no-photo.jpg';};	
    $c = $c + 1;
	$image = "<img src='$image_path' width='120' style='border:0; text-decoration:none'/><br/>";
    $result .= "<a href='/shop/buyer/viewitempage/".$data['artikul']."'>".$image.$data['captiontxt']."</a><br/>".$data['price']."<br/>";
	$result .= "</td>";
   if (($c == 0)  or (($c % $col_count)== 0))
    {  $result .= '</tr>'; };
 };
 $result .= "</table>";
 $paginator = "";
 for ($i = $page_nom-2; $i<=$page_nom+2;$i++)
 {
 if ( ($i>=1) and ($i<=$pages_total)  )
   {
   if ($i != $page_nom)

	{$paginator .= "&nbsp;<a class='pagelink' href='/shop/findbyprice/$priceMQ/$priceLQ/$i'>$i</a>&nbsp;"; }
	else
	{ $paginator .= "&nbsp; $i &nbsp;";};
   }
 };
 $paginator = "Страница $page_nom из $pages_total ".$paginator;
 if ($c==0) {$c = "0"; $result = ''; $paginator = "";};
 $result = "Искали товары дороже $priceMQ и дешевле $priceLQ. Нашли товаров: $items_total<br/>Найдено: $result<br/> $paginator";
 return $result;
 }
 else
 {
   $result = "В границах цен допущена ошибка. <a href='/shop/view'>Исправьте.</a>";
   return $result;
 };
 }
 
 /**
  * \brief Форма поиска по ценам.
  * Считывает форму поиска по ценам из шаблона /classes/Separable/Shop/shop.pricefinder.tpl
  * @return string
  */
 function PriceFinder()
 {
    return $this->components['view']->PasteFile($_SERVER['DOCUMENT_ROOT'].'/classes/Separable/Shop/shop.pricefinder.tpl');	
 }
 
 /**
  * \brief Основной обработчик.
  * Считывает действие от пользователя по полю $_GET['action']
  * запускает соответствующий обработчик и возвращает результат
  * работы обработчика в качестве своего результата.
  * @return string 
  */
 function run()
 {
         $this->components['log']->WriteLog('Shop', 'run');
	 $action = $_GET['action'];
         $this->components['log']->WriteLog('Shop', 'action - ' . $action);
	 switch ($action)
	 {
		
		 case 'fpredzakaz' : {return $this->FPredzakaz(); break;};
		 case 'faddtocart' : {return $this->FAddToCart(); break;};
		 
		 case 'addtocart' : {return $this->addtocart(); break;};
		 case 'clearcart' : {return $this->clearcart(); break;};
		 
		 case 'predzakaz' : {return $this->predzakaz(); break;}
		 
		 case 'view' : {return $this->View(); break;}
		 case 'viewitempage' : {return $this->ViewItemPage(); break;}
                 case 'viewprice'  : {return $this->ViewPrice(); break;}
		 
		 case 'search' : {return $this->Search(); break;}
                 case 'recalc' : {return $this->Recalc(); break;}
		 case 'findbytag' : {return $this->SearchByTag(); break;}
		 case 'findbyprice' : {return $this->SearchByPrice(); break;}
		 
		 default : {return $this->Unknown();}
	 }
 }

}

?>