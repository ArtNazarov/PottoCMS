<?php
if (!defined('APP')) {die('ERROR');};
class FormItems
{
var $form = '';
var $table = null;
var $table_rows = 0;
var $table_cols = 0;
var $hbody = '';
var $hhead = '';
function __construct($params)
{
}
// Список select
function SelectItem($values, $selected_value, $name, $css_class = 'myselect')
{
	$t = " <select class='$css_class' id='$name' name='$name'>";
	foreach ($values as $key => $value)
	{
		$selected = "";
		if ($key == $selected_value) $selected = "selected";
		$t .= " <option value='$key' $selected>$value</option> ";
	}
	$t .= "</select> ";
	$this->view = $t;
	return $this->view;
}
// Переключатели radio
function RadioItems($values, $selected_key, $name,  $css_class = 'myradio')
{
$t = " <div id='g$name' name='$name'>";
	foreach ($values as $key => $value)
	{
		$selected = "";
		if ($key == $selected_key) $selected = "checked='checked'";
		$t .= "<div id='gr$name'><input class='$css_class' type='radio' name='$name' value='$key' $selected>$value</div><br/>";
	}
	$t .= "</div> ";
	$this->view = $t;
	return $this->view;
}
// Флажки checkbox
function CheckboxItems($values, $selected_values, $name,  $css_class = 'mychkbox')
{
$t = " <div id='g$name' name='$name'>";
	foreach ($values as $key => $value)
	{
		$selected = "";
		if (in_array($key, $selected_values)) $selected = "checked='checked'"; $s = '[]';
		$t .= "<div id='gr$name'><input type='checkbox' class='$css_class' name='$name$s' value='$key' $selected>$value</div><br/>";
	}
	$t .= "</div> ";
	$this->view = $t;
	return $this->view;
}

// Облако тегов
// $tags = array ( 'id_tag' => array('tag_count', 'tag_name'));
// other params are min_height, max_height, link, name for div
function TagsCloud($tags, $min_height, $max_height, $link, $name)
{
$counter = 0;
foreach ($tags as $tag_id => $tag_params)
 {
 $counter += $tag_params['tag_count'];
 };
$norma = $max_height / $counter; 
$t = " <div id='g$name' name='$name'>";
	foreach ($tags as $tag_id => $tag_params)
	{
		$tag_name = $tag_params['tag_name'];
		$fs = $min_height+round($norma * $tag_params['tag_count']);
		
		if (rand(1, 100)<50)
		{
		$t .= "&nbsp;<a class='taglink' style='font-size:$fs px' href='$link$tag_id'>$tag_name</a>";
		}
		else
		{
		$t = "<a class='taglink' style='font-size:$fs px' href='$link$tag_id'>$tag_name</a>&nbsp;".$t;
		};
	}
	$t .= "<!-- $counter  --></div> ";
	$this->view = $t;
	return $this->view;
}

function NewTable($nrows, $ncols)
{
$this->table_cols = $ncols;
$this->table_rows = $nrows;
for ($i = 1; $i<=$nrows; $i++)
 for ($j = 1; $j<=$ncols; $j++)
  {$this->table[$i][$j] = '';};  
}

function SetCell($row, $col, $value)
{
$this->table[$row][$col] = $value;
}

function GetTable($name, $width,  $css_class = 'mytable')
{
$t = "<table class='$css_class' id='$name' width='$width'>";
for ($i=1;$i<=$this->table_rows;$i++)
 {
 $t .= "<tr>";
 for ($j=1;$j<=$this->table_cols;$j++)
 
  {
  $cellid = $name.$i.$j;
  $t .= "<td id='$cellid' valign='top' align='left'>".$this->table[$i][$j]."</td>";};
 $t .= "</tr>"; 
 };
$t .= "</table>";
return $t; 
}


function GetForm($action_url, $method, $form_id,  $css_class = 'myform')
{
$t = "<form class='$css_class' action='$action_url' method='$method' id='$form_id' name='$form_id'>".$this->form."</form>";
return $t;
}

function AddToForm($element)
{
$this->form .= $element."<br/>";
}

function TextField($value, $name, $css_class='mytextfield')
{
$t = "<input class='$css_class' type='text' id='$name' name='$name' />";
return $t;
}

function TextArea($value, $rows, $cols, $name, $css_class='mytextarea')
{
$t = "<textarea class='$css_class' name='$name' cols='$cols' rows='$rows'>$value</textarea>";
return $t;
}

function Hidden($value, $name)
{
$t = "<input name='$name' id='$name' type='hidden' value='$value' />";
return $t;
}

function Button($caption, $type, $name, $css_class='mybutton')
{
$t = "<input class='$css_class' name='$name' id='$name' type='$type' value='$caption' />";
return $t;
}

function LabelFor($name, $caption)
{
$t = "<laber for='$name'>$caption</label>";
return $t;
}

function addH($text)
{
$this->hhead .= $text;
}

function addB($text)
{
$this->hbody .= $text;
}

function GetPage()
{
$t = "<html><head>".$this->hhead."</head><body>".$this->hbody."</body></html>";
return $t;
}

function ImagesSelector($path, $full_image, $name, $width="100%", $css_class = "image_selector")
{
    $x = null;
 $helper = new FormItems($x);
 $columns = 3;
 $selected = 'http://'.$_SERVER['SERVER_NAME'].$full_image;
 $file_list = scandir($_SERVER['DOCUMENT_ROOT'].$path);
 $helper->NewTable(count($file_list)/$columns, $columns);
 $view = '';
 for ($i=0; $i<count($file_list); $i++)
	{
	

	if (($file_list[$i]!='..') && ($file_list[$i]!='.') and ($file_list[$i]!=''))
	 if (file_exists($_SERVER['DOCUMENT_ROOT'].$path.$file_list[$i])==true)
	   {
			$p = "http://".$_SERVER['SERVER_NAME'].$path.$file_list[$i];
			$value =  urlencode($p);
			if ($p==$selected)	{$sel = ' checked ';} else {$sel = '';};
			$view = "<div><img height='25%' src='$p' /><input type='radio'  $sel name='img_$name' value='$path${file_list[$i]}'><br/>$p</div>";		
	};
	$helper->SetCell($i/$columns, $i % $columns + 1, $view);
	
	};  
  $text = $helper->GetTable($name, $width);  
  unset($helper);
  return $text;
}


}
?>