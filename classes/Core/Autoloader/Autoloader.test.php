<?php
define('APP',1);
require_once('Autoloader.class.php');
$autoloader = new Autoloader();
$_S = $autoloader->walk(FALSE);
print_r($_S);
$_S = $autoloader->walk(TRUE);
print_r($_S);
//$_S['Log']->WriteLog('test', 'hello!');
