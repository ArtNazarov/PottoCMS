<?php
define('APP', 0);
$mod_path = $_SERVER['DOCUMENT_ROOT'].'/classes/lorius/';
require_once $mod_path . 'lorius.class.php';

$p = null;
$view = new Lorius($p);
$view->UseTpl($mod_path . 'test-lorius.tpl');

$page = 'index'; // default value
if (isset($_GET['page']))
{
    $page = $_GET['page']; // value from URL
};
// routing
switch ($page)
{
    case 'index' :      
    {
        $data['MYTITLE'] = 'test lorius';
        $data['CAPTION'] = 'sample';
        $data['MESSAGE'] = 'Lorem ipsum dolor';
        break;
    };
    case 'about' :      
    {
        $data['MYTITLE'] = 'about';
        $data['CAPTION'] = 'CV';
        $data['MESSAGE'] = 'Etc';
        break;
    };
    default :
    {
        $data['MYTITLE'] = '404';
        $data['CAPTION'] = '404';
        $data['MESSAGE'] = 'Page not found';
    }
};
// render
$view->SetVars($data);
$view->CreateView();
$view->Publish();
?>
