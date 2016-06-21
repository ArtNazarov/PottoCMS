<?php
if (!defined('APP')) {die('ERROR');};
// Модуль для редактирования глобальных блоков

require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core/ClassFactory/ClassFactory.class.php';

class GlobalBlocksController
{
var $components;
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
  $blockname = $_POST['blockname'];
  $blockdescription = $_POST['blockdescription'];
  $blockview = $_POST['blockview'];
  $this->components['db']->setTable('blocks');
  $this->components['db']->Insert('blockname, blockdescription, blockview',
  "'$blockname', '$blockdescription', '$blockview'");
   return "<a href='/admin/blocks'> Успешно сохранено. Вернуться к блокам </a>";
}
function edited()
{
  $blockname = $_POST['blockname'];
  $blockdescription = $_POST['blockdescription'];
  $blockview = $_POST['blockview'];
  $old_blockname = $_POST['old_blockname'];
  $this->components['db']->setTable('blocks');
  $this->components['db']->Update(" blockname='$blockname', blockdescription = '$blockdescription',
   blockview = '$blockview' ", " blockname = '$old_blockname' ");
   return "<a href='/admin/blocks'> Успешно обновлено. Вернуться к блокам </a>";
}
function filledit()
{
  // Считываем из базы
 $blockname = $_POST['blockname'];  
 $this->components['db']->setTable('blocks');
 $this->components['db']->Select('*', " blockname = '$blockname' ");
 $rows = $this->components['db']->Read();  
 $record = $rows[0];
  $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/actions/block.tpl');
  $this->components['view']->SetVars(array(
	 'ACTION' => 'edit',
	 'BLOCKNAME' => $blockname,
	 'BLOCKDESCRIPTION' => $record['blockdescription'],
	 'BLOCKVIEW' =>$record['blockview'],
	 'OLD_BLOCKNAME' => $record['blockname'])
	 );  
  $this->components['view']->CreateView();
  $view = $this->components['view']->GetView(); 
  return $view;
}
function getblockbyname($blockname)
{
 $this->components['db']->setTable('blocks');
 $this->components['db']->Select('*', " blockname = '$blockname' ");
 $rows = $this->components['db']->Read();  
 $record = $rows[0];
 return $record['blockview'];
}
function fillnew()
{   
  $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/actions/block.tpl');
  $this->components['view']->SetVars(
   	array(
	 'ACTION' => 'new',
	 'BLOCKNAME' => '',
	 'BLOCKDESCRIPTION' =>'',
	 'BLOCKVIEW' =>'',
	 'OLD_BLOCKNAME' => '')
	 );  
  $this->components['view']->CreateView();
  $view = $this->components['view']->GetView(); 
  return $view;
}
function delete() // Удаление
{
$blockname = $_POST['blockname'];  
$this->components['db']->setTable('blocks');
$this->components['db']->Delete("blockname='$blockname'");
return "<a href='/admin/blocks'> Успешно удалено. Вернуться к блокам </a>";
}
function view() // Просмотр списка блоков
{
 $items = '';
 $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/items/blockslist.tpl');
 $this->components['db']->setTable('blocks');
 $this->components['db']->Select('*', " 1 = 1 ");
 $rows = $this->components['db']->Read();
 foreach ($rows as $i => $record)
 {
	$this->components['view']->SetVars(
	array(
	 'BLOCKNAME' => $record['blockname'],
	 'BLOCKDESCRIPTION'=>$record['blockdescription'],
	 'BLOCKVIEW'=>$record['blockview']));
	$this->components['view']->CreateView();
	$items .= $this->components['view']->GetView();
 };
 $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/admin/tables/blocks.table.tpl');
 $this->components['view']->SetVar('ITEMS', $items);
 $this->components['view']->CreateView();
 $view = $this->components['view']->GetView();
 return $view;
}


}