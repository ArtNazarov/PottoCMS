<?php
if (!defined('APP')) {die('ERROR styles.class.php');};
// Модуль для редактирования таблиц стилей

require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core/ClassFactory/ClassFactory.class.php';

class Styles
{
var $components;
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
	$this->components['view'] = $this->components['factory']->createInstance("TemplateTool", $params); // Подключаем шаблонизатор	
}
function __destruct()
{
 foreach ($this->components as $key => $value)
	  {
		  unset($this->components[$key]);
	  }
	  unset($this->components);
}

// Новый блок - операция
function newed()
{
  $stylename = $_POST['stylename'];
  $styledescription = $_POST['styledescription'];
  $styleview = $_POST['styleview'];
  $this->components['db']->setTable('styles');
  $this->components['db']->Insert('stylename, styledescription, styleview',
  "'$stylename', '$styledescription', '$styleview'");
   return "<a href='/admin/styles'> Успешно сохранено. Вернуться к блокам </a>";
}
function edited()
{
  $stylename = $_POST['stylename'];
  $styledescription = $_POST['styledescription'];
  $styleview = $_POST['styleview'];
  $old_stylename = $_POST['old_stylename'];
  $this->components['db']->setTable('styles');
  $this->components['db']->Update(" stylename='$stylename', styledescription = '$styledescription',
   styleview = '$styleview' ", " stylename = '$old_stylename' ");
   return "<a href='/admin/styles'> Успешно обновлено. Вернуться к блокам </a>";
}
function filledit()
{
  // Считываем из базы
 $stylename = $_POST['stylename'];  
 $this->components['db']->setTable('styles');
 $this->components['db']->Select('*', " stylename = '$stylename' ");
 $record = $this->components['db']->Read();  
  $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/actions/style.tpl');
  $this->components['view']->SetVars(array(
	 'ACTION' => 'edit',
	 'styleNAME' => $stylename,
	 'styleDESCRIPTION' => $record['styledescription'],
	 'styleVIEW' =>$record['styleview'],
	 'OLD_styleNAME' => $record['stylename'])
	 );  
  $this->components['view']->CreateView();
  $view = $this->components['view']->GetView(); 
  return $view;
}
function fillnew()
{   
  $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/actions/style.tpl');
  $this->components['view']->SetVars(
   	array(
	 'ACTION' => 'new',
	 'styleNAME' => '',
	 'styleDESCRIPTION' =>'',
	 'styleVIEW' =>'',
	 'OLD_styleNAME' => '')
	 );  
  $this->components['view']->CreateView();
  $view = $this->components['view']->GetView(); 
  return $view;
}
function delete() // Удаление
{
$stylename = $_POST['stylename'];  
$this->components['db']->setTable('styles');
$this->components['db']->Delete("stylename='$stylename'");
return "<a href='/admin/styles'> Успешно удалено. Вернуться к блокам </a>";
}
function view() // Просмотр списка блоков
{
 $items = '';
 $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/items/styleslist.tpl');
 $this->components['db']->setTable('styles');
 $this->components['db']->Select('*', " 1 = 1 ");
 $rows = $this->components['db']->Read();
 foreach ($rows as $i => $record)
 {
	$this->components['view']->SetVars(
	array(
	 'styleNAME' => $record['stylename'],
	 'styleDESCRIPTION'=>$record['styledescription'],
	 'styleVIEW'=>$record['styleview']));
	$this->components['view']->CreateView();
	$items .= $this->components['view']->GetView();
 };
 $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/tables/styles.table.tpl');
 $this->components['view']->SetVar('ITEMS', $items);
 $this->components['view']->CreateView();
 $view = $this->components['view']->GetView();
 return $view;
}


}