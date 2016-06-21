<?php
if (!defined('APP')) {die('ERROR reestr.class.php');};
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core/ClassFactory/ClassFactory.class.php';
/**
 * \brief - Поиск подстроки в одном или нескольких полях произвольной таблицы с постраничной выдачей результатов
 * Выполняет поисковый запрос и возвращает результаты с помощью шаблона оформления
 */
class SearchTool
{
        
    /**
     * вспомогательные компоненты
     * @var array[string] of object 
     */
        var $components; 
    /**
     * Имя поля-заголовка (названия) элемента
     * @var string
     */        
        var $attr_t; 
     /**
     * ключ в базе для построения пути к элементу 
     * @var string
     */
        var $attr_k; 
     /**
     * Массив полей, где проводится поиск     
     * @var array of string
     */
        var $attr_s; 
     /**
     * параметры просмотра выдачи
     * @var mixed
     */
        var $view_params; 
        /**
     * число элементов на странице
     * @var string
     */
        var $items_per_page; 
     /**
     * Путь к элементу
     * @var string
     */
        var $urltoitem; 
     /**
     * путь к запросу на поиск
     * @var string
     */
        var $searchurl; 
     /**
     * ключ для GET/POST для поиска
     * @var string 
     */
        var $keysearch;  
     /**
     * ключ для GET/POST номера страницы
     * @var integer
     */
        var $keypagenum; 
     /**
     * шаблон оформления элементов выдачи 
     * @var string
     */
        var $wraptmpl; 
     /**
     * название таблицы, в которой идет поиск
     * @var string 
     */
        var $table; 
     /**
     * число колонок в выдаче
     * @var string
     */
        var $colsinview;
        
        
        public function SearchInTable($t) // Назначать таблицу для поиска
        {
            $this->table = $t;
            return $this;
        }
        
        public function PathToItemBy($k)
        {
            $this->attr_k = $k;
               return $this;
        }
        
        public function OutToTpl($tmpl) // Шаблон оформления элементов выдачи
        {
             $this->wraptmpl = $tmpl;
             return $this;
        }
        
        
        
        public function ItemsPerPage($ipp) // Число элементов на странице
        {
          $this->items_per_page = $ipp;  
          return $this;
        }
        
        public function UsingKeyForSearch($k) // Ключ для поиска
        {                  
             $this->attr_s = $k;
             return $this;
        }
        
        public function UsingKeyForTitle($k) // Ключ для названия
        {
          $this->attr_t = $k;
          return $this;
        }

        
        public function UseSearchUrl($url) // URL, с которого производится поиск
        {
            $this->searchurl = $url;
            return $this;
        }
        public function UseItemUrl($url) // Путь к элементу
        {
            $this->urltoitem = $url;
            return $this;
        }
        public function KeyForSearch($k) // Ключ для GET/POST для получения искомого значения
        {
         $this->keysearch = $k;
          return $this;
        }
        
        public function OfColsCount($k)
        {
            $this->colsinview = $k;
            return $this;
        }
        
        public function KeyForPagenum($k) // Ключ для GET/POST для получения номера страницы
        {
             $this->keypagenum = $k;
              return $this;
        }
        public function ViewOptions($arr) // Отображение полей из базы на значения в шаблоне
        {
          $this->view_params = $arr;   
           return $this;
        }
        public function __construct(&$params)
        {
        $this->components = null;
        $this->components['factory'] = new ClassFactory($params); // Фабрика классов
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
       
         $this->components['view'] = $this->components['factory']->createInstance("TemplateTool", $params);
       
         $this->keysearch = 'searchtext';
         
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
 * \brief Форма поиска.
 * Возвращает HTML-строку с кодом формы для поиска,
 * заполняя шаблон /classes/scorpio/scorpio.search.form.tpl
 * @param string $url - путь к обработчику запроса
 * @param string $button_name - имя кнопки
 * @return string
 */
function SearchForm($url, $button_name)
{
  
   $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/scorpio/scorpio.search.form.tpl');
   $this->components['view']->SetVar('URL', $url);
   $this->components['view']->SetVar('BUTTON_NAME', $button_name);   
   $this->components['view']->CreateView();
   return $this->components['view']->GetView();
}
/**
 * \brief - возвращает параметр запроса по его ключу.
 * Возвращает параметр из $_POST, если он не найден, использует $_GET
 * Если в $_GET ключ не найден, возвращается значение по умолчанию из $def
 * @param string $param
 * @param mixed $def
 * @return mixed
 */
function GetInput($param, $def)
{
  isset ($_POST[$param]) ? $q = $_POST[$param] : $q = '::USEGET::'; // Запрос  
  if ($q=='::USEGET::')
  {
      isset($_GET[$param]) ? $q = $_GET[$param] : $q = $def;
  }
  return $q;
}
/**
 * \brief Заполняет шаблон.
 * Помещает поле записи в соответствующее поле шаблона, если поле записи
 * не существует, используется заполнитель $def
 * @param mixed $data - данные записи
 * @param string $item - поле записи
 * @param string $tempvar - переменная шаблона
 * @param mixed $def  - заполнитель по умолчанию.
 */
function PutInView($data, $item, $tempvar, $def)
{
    
  isset($data[$item]) ? $this->components['view']->SetVar($tempvar, $data[$item]) : $this->components['view']->SetVar($tempvar, $def);  
}
/**
 * \brief Поисковая выдача.
 * Ищет в таблице по заданному ключу и атрибуту и возвращает
 * в качестве результата HTML-разметку с результатами поиска
 * @return string
 */
function  SearchResult() 
{
    
$q = $this->GetInput($this->keysearch, 'ERROR'); // Запрос
$pagenum = $this->GetInput($this->keypagenum, 1); // число страниц
$t = $this->attr_t; // Откуда берем заголовок
$s = $this->attr_s; // Где ищем (по какому полю)

$rq = " ( ";
foreach ($s as $z)
{
    $rq .= " ( $z LIKE '%$q%' ) OR ";
};
 $rq = substr($rq, 0, strrpos($rq, " OR ") ) . " ) ";

$searchreqv_url =  $this->searchurl;
$tmpv = $this->view_params; // отображение полей на шаблон
$item_from = ($this->items_per_page)*($pagenum-1);

/* $rq = $s[0] ." LIKE '%$q%' "; */
$limitation =  " LIMIT $item_from, $this->items_per_page ";

$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'] . $this->wraptmpl );
$this->components['db']->setTable($this->table);
$this->components['db']->Select(" COUNT(*) as cc ", $rq);
$data = $this->components['db']->Read();
$items_count = $data['cc'];
$pages_count = ceil($items_count / $this->items_per_page ) ;
$this->components['db']->Select(" * ", $rq . $limitation);
$items = "<table width='100%' border='0'>";
$c = 1;
$rows = $this->components['db']->Read(); foreach ($rows as $i=>$data)
{
if ($c == 1)  {$items .= "<tr>";};
$c = $c + 1;
$items .= "<td valign='top'>";

$this->PutInView($data, $data[$this->attr_t], 'TITLE', '');
$this->components['view']->SetVar('URL', $this->urltoitem.$data[$this->attr_k]);
 foreach ($tmpv as $df => $tv)
{
if (($tv=='PATH') and ($data[$df]==''))
{
$this->components['view']->SetVar($tv, '/images/no-photo.jpg');
}
else
{
    isset($data[$df]) ? $this->components['view']->SetVar($tv, $data[$df]) : $this->components['view']->SetVar($tv, '');
};
};
$this->components['view']->CreateView();
$items .= $this->components['view']->GetView();

$items .= "</td>";
if ($c > $this->colsinview)
{
 $items .= "</tr>";
 $c = 1;
};

};
$items .= '</table>';
$this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/scorpio/scorpio.search.result.tpl');
$this->components['view']->SetVar('REQUEST', $q);
$this->components['view']->SetVar('ITEMS', $items);
$this->components['view']->SetVar('FINDED', 'Результов всего: '.$items_count . " показано товаров ". $this->items_per_page . " Страниц в выдаче: ". $pages_count);
$this->components['view']->SetVar('PAGENUM', 'Страница выдачи: '.$pagenum);
$this->components['view']->SetVar('PAGESCOUNT', $pages_count);
$p = '<div class="paginator">';
for ($i=($pagenum-3); $i<=($pagenum+3); $i++)
{
    if (($i>=1) && ($i<=$pages_count))
    
    {
        if ($i!=$pagenum)
        {
            $p .= "<a href='$searchreqv_url/$i/$q'>$i</a>&nbsp;&nbsp;";
        }
        else
        {
                $p .= "&nbsp;$i&nbsp;";
        };
    }
    
};
$p .= "</div><br/>";
$this->components['view']->SetVar('PAGINATOR', $p);
$this->components['view']->CreateView();
return $this->components['view']->GetView();
}


}	  

?>
