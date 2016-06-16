<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/classes/formitems/formitems.class.php';

$params = null;

$arr = array (
  "ie" => 'Internet Explorer',
  "opera" => 'Opera browser',
  "mozilla" => "Mozilla Firefox",
  "gchr" => 'Google Chrome');
$selected_item = "mozilla";
$name = "browsers";

$arr2 = array (
  "r" => 'Read',
  "w" => 'Write',
  "d" => "Delete");
  

$selected_items = array("r", "w");  
  
$name2 = "rights";

$tags = array(
 'apps' => array(
    'tag_name' => "Applications",
    'tag_count' =>	24),
'hard' => array(
    'tag_name' => "Hardware",
    'tag_count' =>	12),	
'other' => array(
    'tag_name' => "Other",
    'tag_count' =>	5),	
'google' => array(
    'tag_name' => "Google",
    'tag_count' =>	55),
'unix' => array(
    'tag_name' => "*nix",
    'tag_count' =>	25),
'seo' => array(
    'tag_name' => "SEO",
    'tag_count' =>	31),
'shell' => array(
    'tag_name' => "shell",
    'tag_count' =>	2),	
 );


 

$uf = new FormItems($params);

$uf->NewTable(5, 3);

$uf->SetCell(1, 1, "Список выбора<br/>".$uf->SelectItem($arr, $selected_item, $name));

$uf->SetCell(1, 2, "Переключатели<br/>".$uf->RadioItems($arr, $selected_item, $name));

$uf->SetCell(1, 3,  "Флажки<br/>".$uf->CheckboxItems($arr2, $selected_items, $name2));

$uf->SetCell(2, 1, "Облако тегов<br/>".$uf->TagsCloud($tags, 8, 16, "/findbytag?tag_id=", "tagcloud"));

$uf->SetCell(2, 2,  "Текстовое поле<br/>".$uf->TextField("some-text", "tf1"));

$uf->SetCell(2, 3,  "Многострочный текст<br/>".$uf->TextArea("some-text", 10, 30, "ta1"));

$uf->SetCell(3, 1,  $uf->Button('Отправить', 'submit', 'somebutton'));

$uf->SetCell(4, 2,  "Заполнение текстом");

$uf->SetCell(5, 3,  "Заполнение текстом".$uf->Hidden('value', 'myhiddenfield'));

$uf->AddToForm($uf->GetTable("some_table", "100%"));

$uf->addB("Тест FormItems<br/>Сгенерированная программно страница<br/>");

$uf->addB($uf->GetForm("/bla-bla", "post", "myform1", 'myformstyle'));

$uf->addB($text);

$text = "<title>Тест</title>";

$uf->addH($text);

$style = <<<TTT
<style type='text/css'>
.myformstyle {border:dotted #ccc thin; padding:10px;}
.mybutton {color:#000000; border: #ff0000 thin dashed; background-color:#99CCFF}
</style>
TTT;

$uf->addH($style);

echo $uf->GetPage();

?>