<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config/sysconst.php';
function request_handle($filter)
{
$rq = $_SERVER['REQUEST_URI'];
if (true == isset($_SESSION['hits']))
{
$_SESSION['hits'] = $_SESSION['hits'] + 1;
$mt = microtime(true);
$lasthit = ($mt-$_SESSION['lasthit']);
$_SESSION['lasthit'] = $mt;
$hits = $_SESSION['hits'];
}
else
{
$_SESSION['hits'] = 1;    
$hits = 1;
$_SESSION['lasthit'] = microtime(true);
$lasthit = 999;
};
$lasthit = $lasthit*1000;
$timeout = 15;
if ($filter)
{
    

if (($lasthit<=750) && ($rq!='/shop/view'))
        {            
            echo "<html><head>
            <meta http-equiv='refresh' content='$timeout;$rq'>
            <title>Система защиты</title><meta charset='utf-8'><body>
    <b>Система защиты.</b> 
    <p>Между запросами прошло $lasthit мс. Вы будете автоматически перенаправлены на запрошенную страницу
    через $timeout сек.</p>    
    </body></html>";
            exit;
            die();

            };
            
};            
return $hits;        
}

function statinfo($hits)
{    
$v_ip = $_SERVER['REMOTE_ADDR'];
$ag = $_SERVER['HTTP_USER_AGENT'];
$ru = $_SERVER['REQUEST_URI'];
$v_date = date("l d F H:i:s"); 
$fp = fopen("./logs/ips.log", "a+");
fputs($fp, "IP:$v_ip;REQUEST:$ru;DATE:$v_date;USERAGENT:$ag;HITS:$hits#\n\r\n\r");
fclose($fp);
}

/* GENERAL PROTECTION LAYER */

function dang_symb($str)
{
 $test = false;
 if (strpos($str, "\\")!==false) {$test = true;};
 if (strpos($str, '0x')!==false) {$test = true;};
 if (strpos($str, '/*')!==false) {$test = true;};
 if (strpos($str, '*/')!==false) {$test = true;};
 if (strpos($str, '|')!==false) {$test = true;};
 if (strpos($str, '&&')!==false) {$test = true;};
 if (strpos($str, '--')!==false) {$test = true;};
 if (strpos($str, ':;')!==false) {$test = true;};
 if (strpos($str, ';')!==false) {$test = true;};
 if (strpos($str, '*')!==false) {$test = true;};
 if (strpos($str, ':=')!==false) {$test = true;}; 
 if (strpos($str, '(')!==false) {$test = true;}; 
 if (strpos($str, ')')!==false) {$test = true;}; 
 if (strpos($str, "'")!==false) {$test = true;}; 
 if (strpos($str, ",")!==false) {$test = true;}; 
 if (strpos($str, "#")!==false) {$test = true;}; 
 
 if (strpos($str, "..")!==false) {$test = true;}; 
 if (strpos($str, "/etc/passwd")!==false) {$test = true;}; 
 if (strpos($str, "c:\\")!==false) {$test = true;}; 
 if (strpos($str, "cmd.exe")!==false) {$test = true;}; 
 if (strpos($str, "\\")!==false) {$test = true;}; 
 if (strpos($str, "//")!==false) {$test = true;}; 
 if (strpos($str, "`")!==false) {$test = true;}; 
 if (strpos($str, "$")!==false) {$test = true;}; 
 if (strpos($str, "|")!==false) {$test = true;}; 
  
 
  if (strpos($str, "<")!==false) {$test = true;}; 
  if (strpos($str, ">")!==false) {$test = true;}; 
  if (strpos($str, "[")!==false) {$test = true;}; 
  if (strpos($str, "]")!==false) {$test = true;}; 
  
  if (strpos($str, "~")!==false) {$test = true;}; 
  if (strpos($str, "`")!==false) {$test = true;}; 
  
  if (strpos(strtoupper($str), 'UNION')!==false) {$test=true;};
  if (strpos(strtoupper($str), 'SELECT')!==false) {$test=true;};
  if (strpos(strtoupper($str), 'DROP')!==false) {$test=true;};
  if (strpos(strtoupper($str), 'GROUP')!==false) {$test=true;};  
  if (strpos(strtoupper($str), 'WHERE')!==false) {$test=true;};
  if (strpos(strtoupper($str), 'UPDATE')!==false) {$test=true;};
  if (strpos(strtoupper($str), 'COUNT')!==false) {$test=true;};
  if (strpos(strtoupper($str), 'HAVING')!==false) {$test=true;};
  if (strpos(strtoupper($str), 'SCRIPT')!==false) {$test=true;};
  if (strpos(strtoupper($str), 'DELETE')!==false) {$test=true;};
  if (strpos(strtoupper($str), 'OR ')!==false) {$test=true;};
  if (strpos(strtoupper($str), 'AND ')!==false) {$test=true;};
  if (strpos(strtoupper($str), 'IN ')!==false) {$test=true;};
  if (strpos(strtoupper($str), 'SCRIPT')!==false) {$test=true;};
  if (strpos(strtoupper($str), 'INSERT')!==false) {$test=true;};
  if (strpos(strtoupper($str), 'INTO ')!==false) {$test=true;};
  if (strpos(strtoupper($str), 'VALUES')!==false) {$test=true;};
  if (strpos(strtoupper($str), 'TABLE')!==false) {$test=true;};
  if (strpos(strtoupper($str), 'GRANT')!==false) {$test=true;};
  if (strpos(strtoupper($str), 'ROLLBACK')!==false) {$test=true;};
  if (strpos(strtoupper($str), 'COMMIT')!==false) {$test=true;};
  if (strpos(strtoupper($str), 'CREATE')!==false) {$test=true;};
  
 return $test;
 }
 
function dang_in_arr($arr)
{
 $test_g = false;
 foreach ($arr as $k => $v)
 {
	if (dang_symb($v)===true) {$test_g = true;};
 };
 return $test_g;
}

function test_get()
{
 return dang_in_arr($_GET);
}

function test_post()
{
 return dang_in_arr($_POST);
}

function test_both()
{
	return (test_get() || test_post() );
}

function die_on_dang()
{
	if (test_both()===true) die('Malware symbols detected');
}

function general_protection($filter = true)
{
  if (PROTECTION)
  {
  $hits = request_handle($filter);
  statinfo($hits);
  if ($filter)
  {
  die_on_dang();
  };
  };
}

?>
