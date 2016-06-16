<?php
if (!defined('APP')) {die('ERROR');};
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/masterfactory/masterfactory.class.php';
/** 
 *  Использует таблицу galleries
 * id gcaption gpath
 */
class Gallery
{
    var $components;
    var $role;
    
    function __construct($params)
    {
        $this->components['factory'] = new MasterFactory($params);
        $this->components['view'] =  $this->components['factory']->createInstance("Lorius", $params);	
        $this->components['usr'] =  $this->components['factory']->createInstance("Meerkat", $params);	
        $this->components['db'] =  $this->components['factory']->createInstance("SealDb", $params);	
        $this->components['db']->Plug();
        $username =  $this->components['usr']->GetUserNameFromSession();
        $this->role = $this->components['usr']->GetRole($username);
        $this->rolename =  $this->components['usr']->GetRoleName($this->role);
        if ($this->rolename=="") 
            {$this->rolename='Гость';};
    }
    function ShowGallery()
    {
        $id = $_GET['id'];
        $gallery = $this->GetGallery($id);
        $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/gallery/gallery.tpl');
        $file_list = scandir($_SERVER['DOCUMENT_ROOT']. $gallery['gpath']);
        $items = '';
        foreach ($file_list as $i=>$entry)
        {            
         if (file_exists($_SERVER['DOCUMENT_ROOT']. $gallery['gpath'].'/'.$entry)==true)
                 
         {
             if ( ($entry != '..') && ($entry!='.') )
             {
          $entry = 'http://'.$_SERVER['HTTP_HOST'].$gallery['gpath'].'/'.$entry;         
          $items .= "<img src='$entry' />";          
             };
         };
        };
        $this->components['view']->SetVars(
                array('items'=>$items, 'gcaption'=>$gallery['gcaption']));
        $this->components['view']->CreateView();
        return $this->components['view']->GetView();
    }
    function GetGallery($id)
    {
          $record = array();
          $this->components['db']->setTable('galleries');
          $this->components['db']->Select(' * ', "id='$id'");
          $data = $this->components['db']->Read();
          $record['gcaption']=$data['gcaption'];
          $record['gpath']=$data['gpath'];
          return $record;
    }
    function NewGallery()
    {
        $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/gallery/gallery.body.tpl');
         if ($this->role == 'admin')
         {
        $id = $_POST['id'];
        $gcaption = $_POST['gcaption'];
        $gpath = $_POST['gpath'];
        $this->components['db']->setTable('galleries');
        $this->components['db']->Insert("id, gcaption, gpath",
                "'$id', '$gcaption', '$gpath'");
        $vdata['body'] = "<a href='/gallery/main'>Галерея создана!</a>";
         }
         else
         {
            $vdata['body'] = "Не хватает прав!";
         };
         $vdata['title']  = 'Правка галереи';
      $this->components['view']->SetVars($vdata);
      $this->components['view']->CreateView();
      return $this->components['view']->GetView(); 
    }
    function FirstImage($path)
    {
      $file_list = scandir($_SERVER['DOCUMENT_ROOT']. $path);
      $item = '';
        foreach ($file_list as $i=>$entry)
        {            
         if ( ($entry != '..') && ($entry!='.') )
             {
          $entry = 'http://'.$_SERVER['HTTP_HOST'].$path .'/'.$entry;         
          $item = "<img height='64' src='$entry' />";          
          break;
             };
        };  
      return $item;
    }
    function EditGallery()
    {
        $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/gallery/gallery.body.tpl');
         if ($this->role == 'admin')
         {
        $newid = $_POST['id'];
        $oldid = $_POST['oldid'];
        $gcaption = $_POST['gcaption'];
        $gpath = $_POST['gpath'];
        $this->components['db']->setTable('galleries');
        $this->components['db']->Update("id='$newid',
                gcaption='$gcaption', gpath='$gpath'",
                "id='$oldid'");
        $vdata['body'] = "<a href='/gallery/main'>Галерея обновлена!</a>"; 
         }
         else
         {
          $vdata['body'] = "Недостаточно прав!";
         };
           $vdata['title']  = 'Правка галереи';
      $this->components['view']->SetVars($vdata);
      $this->components['view']->CreateView();
      return $this->components['view']->GetView(); 
    }
    function DeleteGallery()
    {
        $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/gallery/gallery.body.tpl');
        if ($this->role == 'admin')
        {
            $id = $_GET['id'];        
            $this->components['db']->setTable('galleries');
            $this->components['db']->Delete("id='$id'");            
            $vdata['body'] = "<a href='/gallery/main'>Галерея удалена!</a>";
        }
        else
        {
            $vdata['body'] = "Недостаточно прав!";
        }
      $vdata['title']  = 'Удаление галереи';
      $this->components['view']->SetVars($vdata);
      $this->components['view']->CreateView();
      return $this->components['view']->GetView();       
    }
    function ListView()
    {
        
        $this->components['db']->setTable('galleries');
        isset($_GET['page']) ? $page = $_GET['page'] : $page = 1;
        $limit = 10;
        $cols = 3;
        $fromitem = ($page-1)*$limit;        
        $this->components['db']->Select('COUNT(*) AS itemstotal', '1=1');
        $data = $this->components['db']->Read();
        $itemstotal = $data['itemstotal'];
        if ($itemstotal != 0)
        {
            $pagestotal = ceil($itemstotal / $limit);
        }
        else
        {
            $pagestotal = 1;
        };
        $this->components['db']->Select(' * ', " 1 = 1 LIMIT $fromitem, $limit");
        $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/gallery/clientlist.items.tpl');
        $items = "<table width='100%'>";
        $c = 1;
        while ($data = $this->components['db']->Read())
            {
            if ($c == 1) {$items .= "<tr>";};
            $id = $data['id'];
              if ($this->role=='admin')
              
              {
               $data['spec_links'] = "<a href='/gallery/delete/$id'>Удалить</a> &nbsp; <a href='/gallery/fedit/$id'>Правка</a>";                  
                  }
              else
              {$data['spec_links']='';};
                $data['firstimage'] = $this->FirstImage($data['gpath']);
                
                $c = $c + 1;
                
                $this->components['view']->SetVars($data);
                $this->components['view']->CreateView();
                $items .= $this->components['view']->GetView();               
                if ($c == $cols) {$items.="</tr>"; $c=1;}
            };
        $items .= "</table>"    ;
        $paginator = $page . ' из ' . $pagestotal . "&nbsp;|&nbsp;";
        for ($p = ($page-3); $p<=($page+3); $p++)
        {
            if (($p>=1) && ($p<=$pagestotal))
            {
                if ($p!=$page)
                {
                    $paginator .= "&nbsp;<a href='/gallery/$id/$p>$p</a>&nbsp;";
                }
                else
                {
                    $paginator .= $p;
                };
            }
        }
        
        if ($this->role == 'admin')
        {
            $spec_links = "<a href='/gallery/fnew'>Создать галерею</a>";
        }
        else
        {
          $spec_links = '';  
        };
        $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/gallery/clientlist.table.tpl');            
        $this->components['view']->SetVars(
                array(
                    'speclinks' => $spec_links,                  
                    'items'=>$items,
                    'paginator'=>$paginator,
                    'role'=>$this->rolename));
        $this->components['view']->CreateView();
        return $this->components['view']->GetView();
        }
    
    function FNewGallery()
    {
        $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/gallery/gallery.editor.tpl');                    
        $data = array('id'=>'', 'gcaption'=>'', 'gpath'=>'', 'action'=>'/gallery/new');
        $this->components['view']->SetVars($data);
        $this->components['view']->CreateView();
        $vdata['body'] = $this->components['view']->GetView();
        $vdata['title'] = "Создание новой галереи";
        $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/gallery/gallery.body.tpl');        
      $this->components['view']->SetVars($vdata);
      $this->components['view']->CreateView();
        $vdata['body'] = $this->components['view']->GetView();
        $vdata['title'] = "Создание новой галереи";
        $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/gallery/gallery.body.tpl');        
      $this->components['view']->SetVars($vdata);
      $this->components['view']->CreateView();
      return $this->components['view']->GetView();    
    }
    function FEditGallery()
    {
        $id = $_GET['id'];
        $data = $this->GetGallery($id);
        $data['id']=$id;
        $data['oldid']=$id;
        $data['action']='/gallery/edit';
        $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/gallery/gallery.editor.tpl');                    
        $this->components['view']->SetVars($data);
        $this->components['view']->CreateView();
        $vdata['body'] = $this->components['view']->GetView();
        $vdata['title'] = "Правка существующей галереи";
        $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/gallery/gallery.body.tpl');        
      $this->components['view']->SetVars($vdata);
      $this->components['view']->CreateView();
      return $this->components['view']->GetView();   
    }
    function run()
    {
        $action = $_GET['action'];
        switch ($action)
        {
          case 'list'  : {  return $this->ListView(); break; };
          case  'fnew' : { return $this->FNewGallery();break;};
          case  'fedit' :  { return $this->FEditGallery();break;};
    case      'new' : {return $this->NewGallery();break;};          
     case      'edit' : {return $this->EditGallery();break;};
     case       'browse' : {return $this->ListView();break;};
     case       'view' : { return $this->ShowGallery();break;};
     case       'delete' : {return $this->DeleteGallery();break;};
     default : { return "Неизвестное действие!";break;};
        };
    }

    
}

?>
