<?php
define('APP', 0);
$params = null;
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/masterfactory/masterfactory.class.php';
$f = new MasterFactory($params); // Фабрика классов
$chat = $f->createInstance("OkapiChat", $params);
echo $chat->getview();
?>
