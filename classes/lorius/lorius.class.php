<?php  mb_internal_encoding("UTF-8"); // База данных и тексты должны быть в UTF-8
if (!defined('APP')) {die('ERROR');};
class Lorius
{
	var $tpl_file; // Указатель на файл шаблона
	var $tpl_filename; // Имя файла шаблона
	var $template; // Шаблон
	var $tpl_spell; // Массив замен
	var $tpl_global; // Глобальные блоки
        var $tpl_styles; // Список стилей
	var $tpl_view; // Вид страницы
	function __construct($params)
	{
 $tpl_file = null; // Указатель на файл шаблона
 $tpl_filename = ''; // Имя файла шаблона
 $template = ''; // Шаблон
 $tpl_spell = null; // Массив замен
 $tpl_global = null; // Глобальные блоки
 $tpl_view = ''; // Вид страницы
	}
// Деструктор
 function __destruct()
        {
 $this->tpl_file = null;
 unset($this->tpl_file);
 $this->tpl_filename = null;
 unset($this->tpl_filename);
 $this->template = null;
 unset($this->template);
 $this->tpl_spell = null;
 unset($this->tpl_spell);
 $this->tpl_global = null;
 unset($this->tpl_global);
 $this->tpl_view = null;
 unset($this->tpl_view);
}

/*
 * Возвращает строку с подключением единственного стиля $style
 */
function AddStyle($style)
{
    $this->tpl_styles[$style] = "<link type='text/css' rel='stylesheet' href='/style.php?stylename=$style' />";
}

/*
 *  Возвращает строку с подключением перечисленных стилей
 */
function getallstyles()
{
  $styles = "";
  if (is_array($this->tpl_styles) == true)
  {
  foreach ($this->tpl_styles as $s => $h)
  {
      $styles .= $h;
  };
  };
  return $styles;
}


     /**
     * Подключает стандартные констранты
     */
       function PlugStdConstants()
        {
            $dta = getdate( time() );
            /** Возвращает
             * "minutes" - минуты
             * "hours" - часы
             * "mday" - день месяца
             * "wday" - день недели в числовом формате
             * "mon" - месяц в числовом формате
             * "year" - год
             * "dyear" - день года в числовом формате
             * "month" - полное имя месяца
             */
           $rus_mon = array('Январь', 'Февраль', 'Март', 'Апрель',
             'Май', 'Июнь', 'Июль', 'Авгу.рь', 'Декабрь');
           $rus_day = array('Воскресенье', 'Понедельник', 'Вторник',
               'Среда', 'Четверг', 'Пятница', 'Суббота');
           
           $v_ip = $_SERVER['REMOTE_ADDR'];
           $ag = $_SERVER['HTTP_USER_AGENT'];
       
          $voc = array('~YEAR~' => $dta['year'],
                       '~HOURS~' => $dta['hours'],
                       '~MINUTES~' => $dta['minutes'],
                       '~DAY~' => $dta['wday'],
                                                              
                        '~USER_IP~' => $v_ip,
                        '~USER_AGENT~' => $ag,
                        '~MON~' => $dta['mon'],                        
                        '~MONTH~' => $dta['month'],
                         
              
                    );                      
          $this->ReplArr($voc);
        }

/*
 * Использует шаблон
 * Считывает его и заполняет
 * ->tpl_file имя файла
 * ->template текст шаблона
 * Если файл не найден, выводит сообщение об ошибке
 */
	public function UseTpl($aFileName)
	{
		$this->tpl_filename = $aFileName; // Имя файла
		// Открываем файл на чтение
		if ($aFileName == '') {$this->Error_msg('Имя шаблона не может быть пустой строкой');};
		if (file_exists($aFileName))
		{
		$this->tpl_file = $aFileName;
		
$this->template = file_get_contents($aFileName);		// Закрываем файл

		}		
		else
		{
		$this->Error_msg("$y Не найден файл: $aFileName. Аварийный останов...");		
		};
	}
	
/* 
 * Оповещение об ошибке со ссылкой на главную
 */		
function Error_msg($text)
{
         $this->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/actions/sysmessage.tpl');
         $this->SetVar('PAGE', 'Сообщение');
         $this->SetVar('SYS_MESSAGE', $text);
         $this->SetVar('ACTION', '/');
         $this->SetVar('TLINK', 'на главную...');
         $this->CreateView();
         $this->Publish();
		 exit;
		 die($text);
}
/*
 * Оповещение со ссылкой на произвольную страницу
 */
function Redirect_msg($text, $link, $title)
{
    $this->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/actions/sysmessage.tpl');
         $this->SetVar('PAGE', 'Сообщение');
         $this->SetVar('SYS_MESSAGE', $text);
         $this->SetVar('ACTION', "$link");
         $this->SetVar('TLINK', "$title");
         $this->CreateView();
         $this->Publish();
}

/*
 * Возвращает содержимое заданного файла.
 * Если файл не существует или имя файла пустое, выводит
 * сообщение об ошибке
 */
	
	public function PasteFile($aFileName) 
	{
	    if ($aFileName == '') {$this->Error_msg('Имя шаблона не может быть пустой строкой');};
		if (file_exists($aFileName))
		{		
        $tfc = file_get_contents($aFileName);		
		return $tfc;
		}
		else
		{		
		$this->Error_msg("$y Не найден файл: $aFileName. Аварийный останов...");
		};
		
	}
        
/*
 * Присваивает переменной шаблона значение
 */                
	public function SetVar($aParam, $aValue) // Устанавливает значение переменной шаблона
	{
		$this->tpl_spell[$aParam] = $aValue;
	}
/*
 * Псевдоним метода SetVar
 */        
        public function assign($aParam, $aValue)
        {
            $this->SetVar($aParam, $aValue);
        }
        
        /*
         * Возвращает значение, присвоенное переменной шаблона
         */
           
	public function GetVar($aParam) 
	{
	    (isset($this->tpl_spell[$aParam])) ? $r = $this->tpl_spell[$aParam] : $r = "";
		return $r;
	}
	
        /*
* Узнаем, есть ли в шаблоне переменная, полезно при
* публикации виджетов, таких как новые статьи, пользователи онлайн и т.п.	
* т.е. желательно избегать ненужных по шаблону вызовов виджетов
* if  ($this->components['view']->VarPresent('переменная'))
* {... какие-то действия ...}
         */
      
	function VarPresent($aParam) 

	{
	if (strpos($this->template, "~$aParam~")>0) { return true; } else {return false;};
	}
// глобальные переменные	
        
        /*
         * Устанавливает глобальную подстановку (из файла)
         */
	public function SetGlobal($aParam, $aValue)
	{
			$this->tpl_global[$aParam] = $aValue;
	}
        /*
         * Возвращает глобальную подстановку 
         */
	public function GetGlobal($aParam)
	{
			return $this->tpl_global[$aParam];
	}
/*
 *  запись переменных по условию да нет	
 */
	function Choice($aCond, $aVal1, $aVal2)
	{
		if ($aCond==true)
		 { return $aVal1; }
		 else
		 { return $aVal2; };
	}
/*
 *  возвращает обработанный шаблон	
 */
	public function CreateView()
	{
            
                // Подключение стилей
                $this->SetVar('SYS_STYLES', $this->getallstyles());        
            
		// Подстановки вида ~CODE~
		$s = $this->template;
		if (is_null($this->tpl_spell) == false)
		   {
      foreach ($this->tpl_spell as $paramkey => $paramvalue) {
		$s = str_replace("~$paramkey~", $paramvalue, $s);
//		$s = preg_replace("/~".$paramkey."~/", $paramvalue, $s);
		  };
		   };
		// Подстановки вида :CODE:
	if (is_null($this->tpl_global) == false)
		   {
      foreach ($this->tpl_global as $paramkey => $paramvalue) {
		// Открываем файл
		$g = file_get_contents($paramvalue);
		$s = str_replace(":$paramkey:", $g, $s);
//        $s = preg_replace("/:".$paramkey.":/", $g, $s);
		  };  };
// Удаляем Invalid Cont
$s = iconv("UTF-8","UTF-8//IGNORE",$s);
//$s = htmlentities($s, ENT_IGNORE);
//$s = html_entity_decode($s, ENT_IGNORE || ENT_NOQUOTES);
// Fix 0d 0a
// $s = preg_replace("/(\r\n|\r|\n)/", "", $s);
        

        $this->tpl_view = "$s";
	}
	
        /*
         * Присвает значения нескольким переменным сразу
         */        
	function SetVars($vars)
	{
	foreach ($vars as $var_name => $var_value)
	{
		$this->SetVar("$var_name", "$var_value");
	};
	}
        
	/*
         * Выбирает шаблон и присвает переменным значения
         */
	function ffetch($vars, $tpl)
	{
	 $this->UseTpl($tpl);
	 $this->SetVars($vars);
	 $this->CreateView();
	 return $this->GetView();
	}
        
        /*
         * Пост обработчик - заменяет вхождения orig на wrd
         */
        function ReplView($orig, $wrd)
        {
          $view = $this->tpl_view;   
          for ($i=0; $i<=10;$i++)
          {
              $view = str_replace($orig, $wrd, $view);
          };
          $this->tpl_view = $view;
        }
        /*
         * Пост обработчик - заменяет вхождения по словарю
         */
        function ReplArr($voc)
        {
            foreach ($voc as $orig => $wrd)
            {
                $this->ReplView($orig, $wrd);
            };
        }
	/*
         * Назначает текст шаблона напрямую из переменной
         */
	function UsePattern($tpl)
	{
		$this->template = $tpl;
	}
	/*
         * Назначет текст шаблона из переменной и заполняет шаблон
         * значениями из массива
         */
	function pfetch($vars, $tpl)
	{
	 $this->UsePattern($tpl);
	 $this->SetVars($vars);
	 $this->CreateView();
	 return $this->GetView();
	}
	
        /*
         * Выполняет cледующее:
         * 1. Считывает шаблон для строки массива
         * Массив (
         *      ('FIELD1'=>'VALUE11', 'FIELD2'=>'VALUE12'),
         *      ('FIELD1'=>'VALUE21', 'FIELD2'=>'VALUE22'),...)    
         * 2. Результат считывания присваивает переменной view
         * 3. Считывает шаблон обертки $wrap_tpl
         * 4. Заполняет шаблон переменными $vars и 
         * инициализирует переменную ITEM значением view
         * 5. Возвращает результат обработки
         */
	function ffetch_arr($tpl, $arr, $wrap_tpl, $vars)
	{
	$this->UseTpl($tpl);	
	$view = '';
	foreach ($arr as $item)
		{			
			$this->SetVars($item);
			$this->CreateView();
			$view .= $this->GetView();
		};	
        $this->UseTpl($wrap_tpl);        
        $this->SetVars($vars);                
        $this->SetVar('ITEMS', $view);
        $this->CreateView();
        $view = $this->GetView();
        return $view;
	}
        
	/*
         *  1. Считывает шаблон $tpl
         *  2. Считывает данные запроса, обращаясь к объекту $src->Read()
         *  3. Заполняет переменную view
         *  4. Считывает обертку $wrap_tpl
         *  5. Инициализует ее значениями vars и переменную ITEMS
         *  значением view
         */
	function ffetch_src($tpl, $src, $wrap_tpl, $vars)
	{
	$this->UseTpl($tpl);	
	$view = '';
	while ($item = $src->Read())
	{
	foreach ($arr as $item)
		{			
			$this->SetVars($item);
			$this->CreateView();
			$view .= $this->GetView();
		};	
	};
        $this->UseTpl($wrap_tpl);
        $this->SetVars($vars);
        $this->SetVar('ITEMS', $view);
        $this->CreateView();
        $view = $this->GetView();
        return $view;
	}
        
        /*
         *  1. Считывает шаблон из текстовой переменной $tpl
         *  2. Считывает данные запроса, обращаясь к массиву $arr
         *  3. Заполняет переменную view
         *  4. Считывает обертку $wrap_tpl
         *  5. Инициализует ее значениями vars и переменную ITEMS
         *  значением view
         */
        
        function pfetch_arr($tpl, $arr, $wrap_tpl, $vars)
	{
	$this->UsePattern($tpl);	
	$view = '';
	foreach ($arr as $item)
		{			
			$this->SetVars($item);
			$this->CreateView();
			$view .= $this->GetView();
		};	
        $this->UseTpl($wrap_tpl);       
        $this->SetVars($vars);    
        $this->SetVar('ITEMS', $view);
        $this->CreateView();
        $view = $this->GetView();
    return $view;
	}
        
         /*
         *  1. Считывает шаблон из текстовой переменной $tpl
         *  2. Считывает данные запроса, обращаясь к объекту $src->Read()
         *  3. Заполняет переменную view
         *  4. Считывает обертку $wrap_tpl
         *  5. Инициализует ее значениями vars и переменную ITEMS
         *  значением view
         */
	
	function pfetch_src($tpl, $src, $wrap_tpl, $vars)
	{
	$this->UsePattern($tpl);	
	$view = '';
	while ($item = $src->Read())
	{
	foreach ($arr as $item)
		{			
			$this->SetVars($item);
			$this->CreateView();
			$view .= $this->GetView();
		};	
	};
        $this->UseTpl($wrap_tpl);       
        $this->SetVars($vars);    
        $this->SetVar('ITEMS', $view);
        $this->CreateView();
        $view = $this->GetView();
    return $view;
	}
	
	public function GetView()
	{
		return $this->tpl_view;
	}
	 public function Publish()
  {
          // Подключение стандартных констант и переменных
          $this->PlugStdConstants();
	  echo $this->GetView();
  }
         public function Render()
         {
             return $this->GetView();
         }
         public function View()
         {
             $this->Publish();
         }
  
 /*
  * 1. Считывает шаблон $templ_file
  * 2. Получает структуру шаблона из $templ_struc
  * 3. Инициализует переменные значением $data_obj->Read()
  * 4. Возвращает результат обработки 
  */         
  function ITpl($templ_file, $templ_struc, $data_obj)
  {
      $data = $data_obj->Read();
      $this->UseTpl($templ_file);
      $x = array();
      foreach ($templ_struc as $templ_var => $data_var)
      {
        $x[$templ_var] = $data[$data_var];
      };
     $this->SetVars($x);
     $this->CreateView();
     return $this->GetView();
  }
  
  /*
  * Считывает данные, пока $data_obj->Read не вернет NULL
  * 1. Считывает шаблон $templ_file
  * 2. Получает структуру шаблона из $templ_struc
  * 3. В цикле: Инициализует переменные значением $data_obj->Read()
  * 4. Возвращает результат обработки 
  */         
  function MTpl($templ_file, $templ_struc, $data_obj)
  {
      $view = '';
      while ($data = $data_obj->Read())
      {
      $this->UseTpl($templ_file);
      $x = array();
      foreach ($templ_struc as $templ_var => $data_var)
      {
        $x[$templ_var] = $data[$data_var];
      };
     $this->SetVars($x);
     $this->CreateView();
     $view .= $this->GetView();
      };
      return $view;
  }
  
  
  
  
  
}

?>