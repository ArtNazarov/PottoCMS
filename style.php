<?php
define('APP', 0);
$p = null;
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/sealdb/sealdb.class.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/lorius/lorius.class.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/forest/forest.class.php';

$view = new Lorius($p);
$db = new SealDB($p);
$cache = new Forest($p);

$key =  $stylename.'.css';
$stylesheet = '';
if (false == $cache->failed($key))
{
    $stylename = strip_tags($_GET['stylename']);
    $db->setTable('styles');
    $db->Select(' * ', "stylename='$stylename'");
    $rec = $db->Read();
    $stylesheet = $rec['styleview'];
    $cache->save($key, $stylesheet);
}
else
{
    $stylesheet = $cache->get_from_cache($key);
}
header('Content-type: text/css');
echo $stylesheet;

    unset($view);
    unset($db);
    unset($cache);
?>
