<?php // Подключаем класс для карты сайта
define('APP', 0);
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/SiteMapTool/SiteMapTool.class.php';
$params = null;
$sitemap = new SiteMapTool($params);
$sitemap->CreateSitemap();
echo "sitemap created!";
$sitemap = null;
unset($sitemap);
?>