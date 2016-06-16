<?php // Подключаем класс для RSS
define('APP', 0);
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/butterfly/butterfly.class.php';
$params = null;
$rss = new Butterfly($params);
$rss->CreateRss('cms');
echo "rss created!";
$rss = null;
unset($rss)
?>