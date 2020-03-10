<?php
define('APP', 0);
$params = null;
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/ClassFactory/ClassFactory.class.php';
$f = new ClassFactory($params); // Фабрика классов
$chat = $f->createInstance("Chat", $params);
echo $chat->getview();
?>
