<?php
if (!defined('APP')) {die('ERROR');};
/*
 * В файле sysconst.php определены константы для подключения к базе данных
 */
require_once $_SERVER['DOCUMENT_ROOT'].'/config/sysconst.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/duck/duck.class.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/classes/masterfactory/masterfactory.class.php';
class SealDB
{
        var $db_host = DB_HOST; // Хост базы
	var $db_password = DB_PASSWORD; // Пароль
	var $db_user = DB_USER; // Пользователь
	var $db_connection = null; // Соединение
	var $db_name = DB_NAME; // Имя базы данных
	var $db = null; // База данных
	var $db_table = ''; // Имя текущей таблицы
        var $sql_result = null; // Результат запроса
	var $proc = null; // Запросы в уме
	var $params = null; // Параметры запросов
	var $query = '';  // Последний запрос к базе
	var $query_counter = 0; // Подсчитывает число запросов
	var $log = null; // Лог sql запросов!
	var $prefix = DB_PREFIX; // Префикс таблицы
	var $components;
	function __construct($params)
	{
		$this->query_counter = 0;
  	        $this->query = 0;
		$this->log = new Duck($params);
		$this->Plug();
		mysql_query("SET NAMES utf8");
	$this->components = null;
  	$this->components['factory'] = new MasterFactory($params); // Фабрика классов
	$this->components['view'] = $this->components['factory']->createInstance("Lorius", $params);
        $this->log->WriteLog('sql', "sealdb started");
	}
	
/*
 * Сообщает о недопустимом SQL выражении
 */
function dberr_msg()
{
         $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/actions/sysmessage.tpl');
         $this->components['view']->SetVar('PAGE', 'Сообщение');
         $this->components['view']->SetVar('SYS_MESSAGE', 'Попытка проникновения в систему! Аварийное завершение...');
         $this->components['view']->SetVar('ACTION', '/');
         $this->components['view']->SetVar('TLINK', 'на главную...');
         $this->components['view']->CreateView();
         $this->components['view']->Publish();
}
/*
 * Тест на Sql инъекцию
 * Если параметр $text содержит слова insert, delete, select или union,
 * то прекращаем выполнение программы
 */
function sql_protector($text)
{
  $text = strtolower($text); 
  if (
    strpos($text, "insert") Or
    strpos($text, "delete") Or
    strpos($text, "select") Or
    strpos($text, "union")
  ) 
{$this->dberr_msg(); exit; die("Попытка проникновения в систему! Аварийное завершение...");};
}

/*
 * Проверяет подключение к базе данных
 * Если подключение было сброшено, оно создается заново
 */
    function CheckConnention()
	{
     if (connection_status()!=0) { $this->Plug(); };
	}

/*
 * Возвращает очередную строку результата запроса 
 * в виде массива имя поля => значение поля
 * или false, если  больше строк в выборке нет
 */        
	function Read()
	{

		return mysql_fetch_array($this->sql_result);
	}
/*
 * Устанавливает подключение к базе данных.
 * Если соединение не удается установить, выдает ошибку
 */
	function Plug()
	{
            
$this->db_connection=@mysql_connect($this->db_host,$this->db_user,$this->db_password);
if (false == $this->db_connection)        
{
           throw new Exception('Указаны неверные параметры подключения к базе данных');     
}
else
{
        $this->log->WriteLog('sql', "connection established \n");
}
;
/*
 * Если запрашиваемая таблица не существует, выдаем сообщение об ошибке
 */       
$this->db = @mysql_select_db($this->db_name, $this->db_connection);
if (false == $this->db)
{
             throw new Exception("Запрашиваемая таблица ". $this->db_name . " не существует");     
};
	}
/*
 * Освобождает память от выборки
 */
	function Clear()
	{            
		@mysql_free_result($this->sql_result) or die(mysql_error());
		$this->log->WriteLog('sql', 'call mysql_free_result, counter =  '.$this->query_counter."\n");
	}
/*
 * Число строк в выборке
 */
	function NRows()
	{
		$n = mysql_num_rows($this->sql_result);
		return $n;
	}
/*
 * Закрывает соединение
 */
	function Done()
	{
		mysql_close($this->db_connection);		
                $this->log->WriteLog('sql', "call mysql_close \n ");		
	}
/*
 * Выполняет SQL запрос к базе данных
 */        
	function SQL($aQuery)        
	{
		$this->CheckConnention();
		$this->query_counter++;
		$this->log->WriteLog('sql', 'exec query '.$this->query." \n ");		
		$this->sql_result = mysql_query($this->query, $this->db_connection);
	}
/*
 * Назначает рабочую таблицу
 */        
	function setTable($aTable)
	{
		$this->db_table = $this->prefix.$aTable;

	}
/*
 * Получить имя рабочей таблицы
 */        
	function getTable()
	{
		return $this->db_table;
	}
        
        /* 
         * Запрос на выборку полей
         * @param $aFields - список полей или *
         * @param $aCondition - условие и сортировка
         * Пример:
         * <code>
         * $this->Select(' id, title, body ', "id='$id ORDER BY title');
         * </code>
         */
	function Select($aFields, $aCondition)
	{
	    $this->sql_protector($aCondition);
        $table = $this->getTable();
		$this->query = "SELECT $aFields FROM $table WHERE $aCondition;";
		$this->SQL($this->query);
		
	}
        /* 
         * Запрос на добавление записи
         * @param $aFields - список полей 
         * @param $aValues - список значений
         * Пример:
         * <code>
         * $this->Insert('id, title', "'Site page', 'Page title'");
         * </code>
         */
	function Insert($aFields, $aValues)
	{
	    $this->sql_protector($aValues);
		$table = $this->getTable();
		$this->query = "INSERT INTO $table ($aFields ) VALUES ( $aValues);";
        $this->SQL($this->query);
	}
        /* 
         * Запрос на удаление записи        
         * @param $aCondition - условие
         * Пример:
         * <code>
         * $this->Delete("id='$id'");
         * </code>
         */
	function Delete($aCondition)
	{
	    $this->sql_protector($aCondition);
		$table = $this->getTable();
		$this->query = "DELETE FROM $table WHERE $aCondition;";
        $this->SQL($this->query);
	}
        /* 
         * Запрос на обновление записи        
         * @param $aSet - пары поле=значение через запятую
         * @param $aCondition - условие
         * Пример:
         * <code>
         * $this->Update("title='New title'", "id='$id'");
         * </code>
         */
	function Update($aSet, $aCondition)
	{
	$this->sql_protector($aCondition);
	$table = $this->getTable();
	$this->query = "UPDATE $table SET $aSet WHERE $aCondition;";
	$this->SQL($this->query);
	}
        /*
         * Сохраняет текст SQL запроса в массив
         */
	function SetProc($aAlias, $aProc)
	{
	 $this->proc[$aAlias]	= $aProc;
	}
        /*
         * Получает текст SQL запроса из массива
         */
	function GetProc($aAlias)
	{
	 return $this->proc[$aAlias];
	}
        /* 
         * Назначает значение параметру
         */
	function SetParam($aAlias, $aValue)
	{
		$this->params[$aAlias] = $aValue;
	}
        /*
         * Получает значение параметра
         */
	function GetParam($aAlias)
	{
		return $this->params[$aAlias];
	}
        /*
         * Заменяет параметры на значения
         */
	function PrepareSQL($pSQL)
	{
    	$s = $pSQL;
      foreach ($this->params as $paramkey => $paramvalue) {
		$s = str_replace(":$paramkey", $paramvalue, $s);
		  };
		return $s;
	}
        /*
         * Возвращает первое подходящее поле из данной таблицы по заданному условию
         * @param $aField - имя поля
         * @param $aCondition - условие
         * Пример:
         * <code>
         * $this->getCell('id', 'price>10');
         * </code>
         */
	function getCell($aField, $aCondition)
	{
	    $this->sql_protector($aCondition);
	  	$table = $this->db_table;
		$this->query = "SELECT $aField FROM $table WHERE $aCondition;";
		$this->SQL($this->query);
		$data = mysql_fetch_array($this->sql_result);
		$z = $data[$aField];
		$this->Clear();
		return $z;
	}
        /*
         * Простая очистка
         */
	function __destruct()
	{           
           if (connection_status()!=0) {$this->Done();};
           $this->log->WriteLog('sql', "sealdb destructor called");
	}

}

?>