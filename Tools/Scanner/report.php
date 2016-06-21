<?php
define('APP', 0);
$root = $_SERVER['DOCUMENT_ROOT'];
require_once($root . '/classes/Services/UserAuth/UserAuth.class.php');
$cnf = null;
$usr = new Meerkat($cnf);
$username = $usr->GetUsernameFromSession();
$usergroup = $usr->GetRole($username);
if ($usergroup=='admin')
{

function open_page($title)
{
echo "<html><head><title>$title</title><meta charset='utf-8' /></head><body>";
}

function print_text($message, $style, $tag)
{
 echo "<$tag style='$style'>$message</$tag>";
}

function close_page()
{
echo "</body></html>";
}

function print_form()
{
$prev =  date('d.m.y', strtotime(' -1 day'));
$now =  date('d.m.y');
  $form = "<form method='GET' action='/classes/fileutils/report.php'>
  <label for='date1'>Эталон:</label><br/>
  <input type='date' name='date1' value='$prev' /><br/>
  <label for='date1'>Контр. точка:</label><br/>
  <input type='date' name='date2' value='$now' /><br/>
  <input type='submit' value='Проверка' />
  </form>";
  echo $form;
}

function print_arr($arr, $prompt, $empty_msg, $style, $tag)
{
if (count($arr)>0)
{
foreach ($arr as $path => $md5)
	{
echo "<$tag style='$style'>" . $prompt . ':' . $path ."</$tag>";
	};
}
else
{
  echo $empty_msg . '<br/>';
};

}

open_page('Antivir Utility');

print_text( "АНТИВИРУСНАЯ УТИЛИТА, ARTNAZAROV.RU, 2012<br/>", 'font-size:18px; background-color:#CC3333', 'h3');

if ((isset($_GET['date1']) == false ) || (isset($_GET['date2'])==false ))
{
  print_form();
}
else
{
$date_1 = $_GET['date1'];
$date_2 = $_GET['date2'];
$root = $_SERVER['DOCUMENT_ROOT'];
require_once($root . '/classes/fileutils/fileutils.class.php');
$config = null;
$util = new FileUtils($config);

$arr1 = unserialize($util->ReadFile('/logs/sl'.$date_1.'.log'));
$arr2 = unserialize($util->ReadFile('/logs/sl'.$date_2.'.log'));

$arr_changes = array_diff($arr1, $arr2); 

$arr_deleted = array_diff_key($arr1, $arr2);

$arr_newly = array_diff_key($arr2, $arr1);

print_text( 'ЭТАЛОН/ЗЕРКАЛО    :' . $date_1 , 'color:#333333', 'div');
print_text( 'КОНТРОЛЬНАЯ ТОЧКА :' .$date_2 . "<BR/>", 'color:#333333', 'div');
print_arr($arr_changes, 'ПРАВКА:', 'ФАЙЛЫ НЕ ИЗМЕНЯЛИСЬ' , 'color:#339933', 'div');
print_arr($arr_deleted, 'УДАЛЕН:', 'ФАЙЛЫ НЕ УДАЛЯЛИСЬ' , 'color:#FF0000', 'div');
print_arr($arr_newly, 'СОЗДАН/ЗАГРУЖЕН:', 'НОВЫХ ФАЙЛОВ НЕТ' , 'color:#0000FF', 'div');

};

close_page();
}
else
{
echo 'Admin only';
};
?>