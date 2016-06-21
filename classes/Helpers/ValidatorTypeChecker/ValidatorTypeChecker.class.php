<?php
if (!defined('APP')) {die('ERROR rattlesnake.class.php');};
function special_handler($exception) 
    {
      $msg = $exception->getMessage();
      $file = fopen($_SERVER['DOCUMENT_ROOT']. "/logs/exceptions.log", "a+");      
      fwrite($file,  $msg. "\n");
      fclose($file);    
      die( "<HTML><HEAD><meta charset='utf-8'/><TITLE>Error!</TITLE></HEAD><BODY TEXT='#FFFF66' BGCOLOR='#0000FF'>
          <span style='color:#0000ff;background-color:#ffff66'>POTTO CMS KERNEL: СБОЙ</span></br>
          Возникла ошибка. Подробности: $msg 
          <br/>Приносим свои извинения. Отчет об ошибке отправлен администратору.
          </body></html>");
}
class ValidatorTypeChecker
{
// OR USE STANDART PHP FUNCTIONS FILTER_*
var $error;

function _construct($params)
{
	$this->error = array();   
}

function typecheck($param, $tz)
{
$val = false;
switch ($tz)
 {
	case "arr" : { $val = is_array($param); break; };
	case "int" : { $val = is_int($param); break; };
	case "real" : { $val = is_float($param); break; };
	case "str"  : { $val = is_string($param); break; };
	case "obj"  : { $val = is_object($param); break; };
	case "num"  : { $val = is_numeric($param); break; };
	case "pint" : { if (is_integer($param))
							{ 
							$val = ($param > 0);
					};		
					break; };	
	case "preal" : { if (is_float($param))
							{ $val = ($param > 0);};		
					break; };						
 };
return $val;
}

function between($val, $start, $end)
{
return (($val>=$start)&&($val<=$end));
}

function inarr($var, $arr)
{
$val = false;
foreach ($arr as $x)
 { 
	if ($x == $var) {$val = true; break;};
 };
return $val;
}

function classcheck($param, $cl)
{
if (true == isset($param))
{
        if (true==class_exists($cl))
            {
                return ($param instanceof $cl);
            }
        else
        {
                return false;
         }
}
    else
    {
        return false;
    };
}

function std_filter($var)
{
 return htmlspecialchars(addslashes(trim($var)));
}

function strict_filter($var)
{
	return htmlspecialchars(addslashes(trim(strip_tags($var))));
}

function getmember($arr, $name, $def)
{
    if (isset($arr)==true)        
    { isset($arr["$name"]) ? $value = $arr["$name"] : $value = $def;                 
        return $this->strict_filter($value);    
    }
    else
    {        
        return $this->strict_filter($def);
    };
    
}

function getvariable($var, $def)
{
    $value = NULL;
    isset($var) ? $value = $var : $value = $def; 
    return $this->strict_filter($value);
}



function arr_sanity($tz, $arr, $member, $def)
{
  $value = $this->getmember($arr, $member, $def);
  if ($this->typecheck($value, $tz)==true)  
	{
		return $value;
	}
	else
	{
		$this->error = array('error'=>'$value is not $tz');
		return NULL;
	};


}

function var_sanity($tz, $var, $def)
{
   $value = $this->getvariable($var, $def);
   if ($this->typecheck($var, $tz)==true)  
	{
		return $value;
	}
	else
	{
		$this->error = array('error'=>"$var is not $tz");
		return NULL;
	};


}

function boolmsg($var, $true_mess, $false_mess)
{
    if ($var == true)
    {
        echo $true_mess;  
    }
    else
    {
        echo $false_mess;
    };
}



}

?>
