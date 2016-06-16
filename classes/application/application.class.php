<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/masterfactory/masterfactory.class.php';
class application
{
 var $components;
   function __construct($params)
   {
	 $this->components['factory'] = new MasterFactory($params);
     $this->components['usr'] =  $this->components['factory']->createInstance("Meerkat", $params);	
    }
	  
   function __destruct()
   {
     foreach ($this->components as $key => $value)
	  {
		  unset($this->components[$key]);
	  };
	  unset($this->components);	  
   }
   
    function run($class, $method)
    {    	
session_start();
$not_admin_class = ($class !== 'Colibri');	
$usr_role = $this->components['usr']->GetRoleFS();
$admin_session = ($usr_role=='admin') or ($usr_role=='seller');
$ukey = "guest";
if (array_key_exists('ukey', $_SESSION)) {$ukey = $_SESSION['ukey'];};
$doc = $_SERVER['DOCUMENT_ROOT'] . '/webdocs/' . $ukey. md5($_SERVER['REQUEST_URI'] . $_SERVER['REMOTE_ADDR'].'.html');
$doc_exists = file_exists($doc);
if (!$doc_exists) {$not_expired = false;} else { $not_expired = ((time()-filemtime($doc))<=CACHE_LIFETIME);};
$not_guest = ($ukey!=="");
if ($doc_exists and $not_expired and $not_admin_class and !$admin_session)
{
	echo file_get_contents($doc);
	echo "<!-- Cached data --!>";
	exit;
}
else
{
$params = null;
$time_start = microtime(true); // НАЧАЛО СЦЕНАРИЯ
require_once $_SERVER['DOCUMENT_ROOT'].'/protection/general.php'; // ФИЛЬТРЫ ПЕРЕМЕННЫХ
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/meerkat/meerkat.class.php'; // ФИЛЬТРЫ ПЕРЕМЕННЫХ
$usr = new Meerkat($params);
$usr_role = $usr->GetRoleFS();
$filter = ($usr_role=='admin') or ( $usr_role=='seller');
general_protection(!$filter);
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/rattlesnake/rattlesnake.class.php';
set_exception_handler('special_handler');
include_once $_SERVER['DOCUMENT_ROOT'].'/iofilters/filterio.php'; // ФИЛЬТР ВВОДА
require_once $_SERVER['DOCUMENT_ROOT'].'/iofilters/filtervars.php'; // ФИЛЬТРЫ ПЕРЕМЕННЫХ
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/masterfactory/masterfactory.class.php'; // ФАБРИКА КЛАССОВ
$f = new MasterFactory($params);
$site = $f->createInstance($class, $params);
$site->$method(); // Замер производительности
$time_end = microtime(true); // ЗАВЕРШЕНИЕ СЦЕНАРИЯ
$wt = $time_end - $time_start;
$log = $f->createInstance('duck', $params);
$log->WriteLog('timing', $wt . "\n");
echo "<!-- Real request --!>";
if (($not_admin_class == true) and ($admin_session == false))
{
$fh = fopen($doc, "w+");
flock($fh, LOCK_EX);
fwrite($fh, $site->components['view']->GetView());
flock($fh, LOCK_UN);
fclose($fh);
};
};
    }
}
?>
