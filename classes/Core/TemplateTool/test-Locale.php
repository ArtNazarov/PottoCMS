<?php
define('APP', 0);
$mod_path = __DIR__ . '/';
require_once $mod_path . 'TemplateTool.class.php';
$p = null;
$view = new TemplateTool($p);
$view->UseTpl($mod_path . 'test.tpl');
$lang = 'rus';
isset($_GET['lang']) ? $lang = $_GET['lang'] : $lang = 'rus';
$translation = parse_ini_file($mod_path . $lang . '.ini');
$view->SetVars($translation);
$view->CreateView();
$view->Publish();
?>
