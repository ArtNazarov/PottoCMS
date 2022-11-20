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
$fp = fopen( $_SERVER['DOCUMENT_ROOT']."/logs/ips.log", "a+");
fputs($fp, "IP:$v_ip;REQUEST:$ru;DATE:$v_date;USERAGENT:$ag;HITS:$hits#\n\r\n\r");
fclose($fp);
}

/* GENERAL PROTECTION LAYER */

function  die_on($str,  $test){
    
   if (strpos(strtoupper($str), strtoupper($test))!==false) {
       echo "Detected " . $test;
       return true;
   };
   return false;
}


function custom_stop($str){
    if (
             (strpos(strtoupper($str), 'DELETE')!==false)
          && ( strpos(strtoupper($str), 'ORDERDELETE')===false) 
          
          ) {
         echo "No delete";
         return true;
          };
         return false;
  
}

function dang_symb($str)
{
 $test = false; 
 $test = custom_stop($str);
 $test=die_on($str,'0x');
 $test=die_on($str,'/*');
 $test=die_on($str,'*/');
 $test=die_on($str,'|');
 $test=die_on($str,'&&');
 $test=die_on($str,'--');
 $test=die_on($str,':;');
 //die_on($str,';');
 //die_on($str,'*');
 $test=die_on($str,':=');
 //die_on($str,'(');
 //die_on($str,')');
 //die_on($str,"'");
 //die_on($str,",");
 //die_on($str,"#");
 
 //$test=die_on($str,"..");
 $test=die_on($str,"/etc/passwd");
 $test=die_on($str,"c:\\");
 $test=die_on($str,"cmd.exe");
 $test=die_on($str,"\\");
 $test=die_on($str,"//");
 $test=die_on($str,"`");
 $test=die_on($str,"$");
 $test=die_on($str,"|");
  
 
 // $test=die_on($str,"<");
//  $test=die_on($str,">");
  $test=die_on($str,"[");
  $test=die_on($str,"]");
  
  $test=die_on($str,"~");
  $test=die_on($str,"`");
  
  $test=die_on($str, 'UNION');
  $test=die_on($str,'SELECT');
  $test=die_on($str,'DROP');
  $test=die_on($str,'GROUP');  
  $test=die_on($str,'WHERE');
  $test=die_on($str,'UPDATE');
  $test=die_on($str,'COUNT');
  $test=die_on($str,'HAVING');
  $test=die_on($str,'SCRIPT');
  
   
  $test=die_on($str,'OR ');
  $test=die_on($str,'AND ');
  $test=die_on($str,'IN ');
  $test=die_on($str,'SCRIPT');
  $test=die_on($str,'INSERT');
  $test=die_on($str,'INTO ');
  $test=die_on($str,'VALUES');
  $test=die_on($str,'TABLE');
  $test=die_on($str,'GRANT');
  $test=die_on($str,'ROLLBACK');
  $test=die_on($str,'COMMIT');
  $test=die_on($str,'CREATE');
  
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
  $test=die_on_dang();
  };
  };
}

?>
