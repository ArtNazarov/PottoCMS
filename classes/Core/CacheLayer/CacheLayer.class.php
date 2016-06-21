<?php
if (!defined('APP')) {die('ERROR');};
/**
 * \brief Файловый кэш сериализованных объектов
 * Класс для хранения сериализованных переменных в файлах
 */
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core/FileUtils/FileUtils.class.php';
class CacheLayer extends FileUtils
{    
    /**
     * Путь к каталогу с кэшем переменных
     */
var $path;
    /**
     * Расширение файлов кэша
     */
var $ext;
    /**
     * Признак, что кэщ устарел
     */
var $expired;
    /**
     * Время жизни (в миллисекундах) кэша
     */
var $lifetime;
    /**
     * Значение переменной
     */
var $value; 
/**
 * Конструктор - устанавливает расширение файлов сериализации,
 * назначает каталог по умолчанию и время жизни кешированного
 * значения в файле
 * если каталог кэша переменных не существует, он будет создан
 * @param type $params - параметры конструктора 
 */
function __construct($params)
{
parent::__construct($params);
$this->ext = '.var';
$this->expired = false;
$this->path = '/var_cache/';
$this->lifetime = 6; // Values in seconds
/*
if (! is_dir($this->path)) {
    
    mkdir($this->path); 
    chmod($this->path, 0777);};
 * 
 */
}

/**
 * \brief Кэш не найден или устарел?
 * Возвращает false, если ключ устарел или файл не был найден
 * @params $key - ключ
 */
function failed($key)
{
    
$v = false;
$filename = $this->path . $key . $this->ext;
if (file_exists($filename)==false)
 {
  $v = true;
 }
 else if ((time()- filemtime($filename) ) > $this->lifetime)
 {  
 $v = true;
 };
return $v; 
}
/**
 * \brief Записать значение переменной
 * Сохраняет значение переменной для заданного ключа
 * @params $key - ключ
 */
function save($key, $value)
{
$this->clear($key);
$this->value = $value;
$this->WriteFile(
        array('filename' =>  $this->path . $key . $this->ext, 
              'message' => serialize($value),
              'mode' => array(
                  'rw' => 'w+',
                  'with_date' => false)
              ));
}
/**
 * \brief Очистить значение переменной
 * Очищает файл с заданным ключом
 * @params $key - ключ
 */
function clear($key)
{
$filename = $this->path . $key . $this->ext;
$this->ClearFile($filename);
}
/**
 * \brief Получить значение переменной
 * Извлекает значение переменной по ключу
 * @params $key - ключ
 */
function get_from_cache($key)
{
$filename = $this->path . $key . $this->ext;
$this->value =  unserialize($this->ReadFile($filename));	 
return $this->value;
}
}
?>