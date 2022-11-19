<?php // Генератор карты сайта sitemap.xml
// Для базы
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core/DatabaseLayer/DatabaseLayer.class.php';
// Для шаблонов
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core/TemplateTool/TemplateTool.class.php';
class SiteMapTool
{
 var $m;
 var $o;
 var $mod_path = __DIR__ . '/';
 function __construct(array $params)
 {
	  // Настройки шаблонизатора
	 $this->v = new TemplateTool($params);
	 // Настройки базы данных
 	 $this->m = new DatabaseLayer($params);
 	 $this->m->Plug();
 }
 function __destruct()
 {
 $this->v->__destruct();
 unset($this->v);
 $this->m->__destruct();
 unset($this->m);
 }
 function CreateSitemap()
 {
	 $sitemap = "";
     $urls = "";
     $this->v->UseTpl($this->mod_path.'urlitem.tpl');
	 // Для категорий
	 $this->m->setTable('categories');
	 $this->m->Select('category', '1=1');
	 $rows = $this->m->Read();
         foreach ($rows as $index => $data )
	 {
 	   $url = $data['category'];
       $this->v->SetVar('URL', 'http://'.$_SERVER["SERVER_NAME"]."/content/category/".$url);
       $this->v->SetVar('FREQ', 'weekly');
	   $this->v->CreateView();
	   $urls .= $this->v->GetView();
	 }
	 //$this->m->Clear();
	 // Для страниц сайта
	 $this->m->setTable('pages');
 	 $this->m->Select('id', '1=1');
         $rows = $this->m->Read();
         foreach ($rows as $index => $data )
	 {
 	   $url = $data['id'];
       $this->v->SetVar('URL', 'http://'.$_SERVER["SERVER_NAME"]."/content/".$url);
       $this->v->SetVar('FREQ', 'weekly');
	   $this->v->CreateView();
	   $urls .= $this->v->GetView();
	 }
   $this->m->Done();
     // Пишем карту сайта
    $this->v->UseTpl($this->mod_path.'sitemap.tpl');
	$this->v->SetVar('URLS', $urls);
	$this->v->CreateView();
	$sitemap = $this->v->GetView();
    $fp = fopen ($_SERVER['DOCUMENT_ROOT'].'/sitemap.xml', "w+");
    fwrite ($fp, $sitemap);
    fclose ($fp);
 }
}

?>