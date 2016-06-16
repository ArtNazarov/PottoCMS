<?php
if (!defined('APP')) {die('ERROR');};
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/lorius/lorius.class.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/collections/widgets.collection.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/forest/forest.class.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/fileutils/fileutils.class.php';
class FileFerret extends FileUtils
{

	var $v;
	var $var_cache;
	var $ui;
	var $action;
	var $dirobject;
	var $mod_path = '/classes/fileferret/';
        
          function GetPost($param, $def)
     {
         $result = '';
         isset($_POST[$param]) ? $result = $_POST[$param] : $result = $def;
         return $result;
     }

        
	function __construct($params)
	{
                parent::__construct($params);
		$this->v = new Lorius($params);
		$this->var_cache = new Forest($params);
	}
	function __destruct()
	{
	unset($this->v);
	}
	
	
	function setParams()
	{
	$quota = 2000; //Mb	
	if ($this->var_cache->failed('du')==true)
	{
	$du = round($this->gds($_SERVER['DOCUMENT_ROOT'])/(1024*1024), 2); // Использовано
	$this->var_cache->save('du', $du);
	}
	else
	{
	$du = $this->var_cache->get_from_cache('du');
	};
	$df = $quota - $du;
	$percent = round($du / $quota, 2);
	$this->v->SetVar('INDICATOR', 
		w_progress_bar(460, 20, $percent, "Ост: $df | Исп:$du Mb ", '00BB00', 'CCCCCC'));
		if ($this->GetPost('action', '')!="")
		{
		$this->setAction($this->GetPost('action', ''));
		$this->setDirObject($this->GetPost('dirobject', ''));
		}
		else
		{
			$this->setAction('listdir');
  		    $this->setDirObject($_SERVER['DOCUMENT_ROOT'].'/templates');
		};
	}
	function getUI()
	{
		return $this->ui;
	}
	function setUI($anUI)
	{
		$this->ui = $anUI;
	}
	function ListDir($aDir)
	{
            
            $fu = $this->DirScan($aDir);                       
            $this->v->UseTpl($_SERVER['DOCUMENT_ROOT'].$this->mod_path.'file.items.tpl');
            $items = '';
            foreach ($fu as $item)
            {
                $this->v->SetVars($item);
                $this->v->CreateView();
                $items .= $this->v->GetView();
            };
           	
		$this->v->UseTpl($_SERVER['DOCUMENT_ROOT'].$this->mod_path.'filemanagerui.tpl');
		$this->v->SetVars(array('LIST' => $items,
		'ACTION'=> $this->getAction(),
		'DIROBJECT'=> $aDir,
		'SELOBJECTS' => '',
		'CLIPDIR' => $this->GetPost('clipdir', ''),
		'CLIPSEL' => $this->GetPost('clipsel', ''),
                'CLIPFLAG' => $this->GetPost('clipflag', '')
                    ));
		$this->v->CreateView();
		$this->setUI($this->v->GetView());
	}
        
        
        function PrepareUnZip()
        {
            $this->v->UseTpl($_SERVER['DOCUMENT_ROOT'].$this->mod_path.'beforeunzip.tpl');
            $this->v->SetVars(array('ACTION' => 'unzip',
	'DIROBJECT' => $this->GetPost('dirobject', ''),
	'SELOBJECTS'=> '',
	'CLIPDIR' => $this->GetPost('clipdir', ''),
	'CLIPSEL' => $this->GetPost('clipsel', ''),
   'CLIPFLAG' => $this->GetPost('clipflag', '')));
	$this->v->CreateView();
	$this->setUI($this->v->GetView());
        }
        
        function UnZip()
        {
           
            $this->v->UseTpl($_SERVER['DOCUMENT_ROOT'].$this->mod_path.'unzip.tpl');
            $ExtractFolder = $_POST['folderextract'];
            $ZipArchive = $_POST['ziparchive'];
            
            
            $this->UnArch($ZipArchive, $ExtractFolder);
        
            
            $this->v->SetVars(array(
                'MESSAGE' => "Архив $ZipArchive распакован в $ExtractFolder",
		'ACTION'=> $this->getAction(),
		'DIROBJECT'=> $aDir,
		'SELOBJECTS' => '',
		'CLIPDIR' => $this->GetPost('clipdir', ''),
		'CLIPSEL' => $this->GetPost('clipsel', ''),
                'CLIPFLAG' => $this->GetPost('clipflag', '')
                    ));
		$this->v->CreateView();
		$this->setUI($this->v->GetView());
        }

	function PrepareUpload()
	{
	$this->v->UseTpl($_SERVER['DOCUMENT_ROOT'].$this->mod_path.'uploader.tpl');
	$this->v->SetVars(array('ACTION' => 'uploading',
	'DIROBJECT' => $this->GetPost('dirobject', ''),
	'SELOBJECTS'=> '',
	'CLIPDIR' => $this->GetPost('clipdir', ''),
	'CLIPSEL' => $this->GetPost('clipsel', ''),
   'CLIPFLAG' => $this->GetPost('clipflag', '')));
	$this->v->CreateView();
	$this->setUI($this->v->GetView());
	}
	
	function PrepareRename()
	{
	$this->v->UseTpl($_SERVER['DOCUMENT_ROOT'].$this->mod_path.'filerename.tpl');
	$this->v->SetVars(array('ACTION' => 'renaming',
	'DIROBJECT' => $this->GetPost('dirobject', ''),
	'SELOBJECTS'=> '',
	'CLIPDIR'=> $this->GetPost('clipdir', ''),
	'CLIPSEL'=> $this->GetPost('clipsel', ''),
   'CLIPFLAG'=> $this->GetPost('clipflag', '')));
	$this->v->CreateView();
	$this->setUI($this->v->GetView());
	}
	
	function PrepareChmod()
	{
	$this->v->UseTpl($_SERVER['DOCUMENT_ROOT'].$this->mod_path.'filechmod.tpl');
	$this->v->SetVars(array('ACTION' => 'chmoding',
	'DIROBJECT'=> $this->GetPost('dirobject', ''),
	'SELOBJECTS'=> '',
	'CLIPDIR'=> $this->GetPost('clipdir', ''),
	'CLIPSEL'=> $this->GetPost('clipsel', ''),
   	'CLIPFLAG'=> $this->GetPost('clipflag', '')));
	$this->v->CreateView();
	$this->setUI($this->v->GetView());
	}


	function MkDirUI()
	{
	$this->v->UseTpl($_SERVER['DOCUMENT_ROOT'].$this->mod_path.'mkdir.tpl');
	$this->v->SetVars(array('ACTION' => 'newdir',
	'DIROBJECT'=> $this->GetPost('dirobject', ''),
	'SELOBJECTS'=> '',
	'CLIPDIR'=> $this->GetPost('clipdir', ''),
	'CLIPSEL'=> $this->GetPost('clipsel', ''),
   	'CLIPFLAG'=> $this->GetPost('clipflag', '')));
	$this->v->CreateView();
	$this->setUI($this->v->GetView());
	}

		function NewDir()
	{

	$dir = $this->GetPost('dirobject', '');
	$dirname = $_POST['dirname'];

	mkdir($dir."/".$dirname, 0777);

	$this->v->UseTpl($_SERVER['DOCUMENT_ROOT'].$this->mod_path.'newdir.tpl');
	$this->v->SetVars(array('ACTION' => 'listdir',
	'DIROBJECT'=> $dir."/".$dirname,
	'SELOBJECTS'=> '',
	'NEWDIRECTORY'=> $dir."/".$dirname,
	'CLIPDIR'=> $this->GetPost('clipdir', ''),
	'CLIPSEL'=> $this->GetPost('clipsel', ''),
   	'CLIPFLAG'=> $this->GetPost('clipflag', '')));
	$this->v->CreateView();
	$this->setUI($this->v->GetView());
	}

	function Uploading()
	{
  $dir = $this->GetPost('dirobject', '');
  $count = count($_FILES['fupload']['name']);
  $filelist = "К загрузке файлов: $count<br/>";
  for ($i = 0; $i < $count; $i++) {
  $source = $_FILES['fupload']['tmp_name'][$i];
  $target = $dir.'/'.$_FILES['fupload']['name'][$i];
  move_uploaded_file($source, $target);
  $filelist .=  "Файл: $target<br/>";
  }
   $this->v->UseTpl($_SERVER['DOCUMENT_ROOT'].$this->mod_path.'uploaded.tpl');
	$this->v->SetVars(array('ACTION' => 'listdir',
'DIROBJECT' => $this->GetPost('dirobject', ''),
'SELOBJECTS' => '',
'FILELIST' => $filelist,
'CLIPDIR' => $this->GetPost('clipdir', ''),
'CLIPSEL' => $this->GetPost('clipsel', ''),
'CLIPFLAG' => $this->GetPost('clipflag', '')));
   $this->v->CreateView();
	$this->setUI($this->v->GetView());
		}





   function DeleteFiles()
   {
   // Функция удаления файлов
   $files = explode('; ', $_POST['selobjects']);
   $dir = $this->GetPost('dirobject', '');
   $st = '';
   foreach ($files as $fname)
    {
    if ($fname<>'') {
    	$filename = $dir.'/'.$fname;
    	$st .= $filename."<br/>";
    	if (is_dir($filename)) {$this->full_del_dir($filename);}
    	else { 	unlink($filename);};
    	};
    };
    $this->v->UseTpl($_SERVER['DOCUMENT_ROOT'].$this->mod_path.'deletefiles.tpl');
   	$this->v->SetVars(array('FILENAMES' => $st,
	'ACTION'=> 'listdir',
	'DIROBJECT'=> $this->GetPost('dirobject', ''),
	'SELOBJECTS'=> '',
	'CLIPDIR'=> $this->GetPost('clipdir', ''),
	'CLIPSEL'=> $this->GetPost('clipsel', ''),
   'CLIPFLAG'=> $this->GetPost('clipflag', '')));
    $this->v->CreateView();
	$this->setUI($this->v->GetView());

  	}



	function Paste()
	{
	$flag =	$this->GetPost('clipflag', '');

   $files = explode('; ', $this->GetPost('clipsel', '')); // не selobjects!

   $sourcedir = $this->GetPost('clipdir', ''); // Источник
   $targetdir = $this->GetPost('dirobject', ''); // Приемник

   $st = '';
   foreach ($files as $fname)
    {

    if ($fname<>'') {

    	$sourcefilename = $sourcedir.'/'.$fname;
    	$targetfilename = $targetdir.'/'.$fname;

    	$st .= "$sourcefilename в $targetfilename <br/>";

    	if (is_dir($sourcefilename))
		{
          if ($flag == 'copy') {
            // Копирование каталогов
          	$this->rcopy($sourcefilename, $targetfilename);
          };
                    if ($flag == 'move')
                     {
                    	// Перемещение каталогов
                    	// Копируем источник
                    	$this->rcopy($sourcefilename, $targetfilename);
                    	// Удаляем источник
                    	$this->full_del_dir($sourcefilename);
                     };
			}
    	else {
			if ($flag == 'copy') { copy($sourcefilename, $targetfilename);};
			if ($flag == 'move') {rename($sourcefilename, $targetfilename);};
			};

    	};     	// end if



    }; // end foreach


	$this->v->UseTpl($_SERVER['DOCUMENT_ROOT'].$this->mod_path.'paste.tpl');
   	$this->v->SetVar('FILENAMES', $st);
	if ($this->GetPost('clipflag', '') == 'copy') {
	   	$this->v->SetVar('MESSAGE', ' скопированы ');
	}
	else
	{ 	$this->v->SetVar('FILENAMES', ' перемещены ');};

	$this->v->SetVars(array('ACTION' => 'listdir',
	'SOURCEDIR'=> $sourcedir,
	'TARGETDIR'=> $targetdir,
	'DIROBJECT'=> $this->GetPost('dirobject', ''),
	'SELOBJECTS'=> '',
	'CLIPDIR'=> $this->GetPost('clipdir', ''),
	'CLIPSEL'=> $this->GetPost('clipsel', ''),
   	'CLIPFLAG'=> $this->GetPost('clipflag', '')));
    $this->v->CreateView();
	$this->setUI($this->v->GetView());
	}

	function EditFile()
	{
    $filename =  $_POST['selobjects'];
	$this->v->UseTpl($_SERVER['DOCUMENT_ROOT'].$this->mod_path.'fileeditor.tpl');
	$this->v->SetVars(array('ACTION' => 'savefile',
	'FILENAME'=> mb_substr($_POST['selobjects'], 1+mb_strrpos($_POST['selobjects'], '/')),
  'LINES'=> file_get_contents($filename),
	'DIROBJECT'=> $this->GetPost('dirobject', ''),
	'SELOBJECTS'=> '',
	'CLIPDIR'=> $this->GetPost('clipdir', ''),
	'CLIPSEL'=> $this->GetPost('clipsel', ''),
   'CLIPFLAG'=> $this->GetPost('clipflag', '')));
	$this->v->CreateView();
	$this->setUI($this->v->GetView());

	}
	
		function Renaming()
	{

	$this->v->UseTpl($_SERVER['DOCUMENT_ROOT'].$this->mod_path.'filerenamed.tpl');
	$this->v->SetVar('ACTION', 'listdir');
	
	$old_filename = $this->GetPost('dirobject', '').'/'.mb_substr($this->GetPost('clipsel', ''), 1+mb_strrpos($this->GetPost('clipsel', ''), '/'));
    $new_filename =	$this->GetPost('dirobject', '').'/'.$_POST['newname'];
	
	rename ($old_filename, $new_filename);
	
	$this->v->SetVars(array('OLD_NAME' => $old_filename,
	'NEW_NAME' => $new_filename,    
	'DIROBJECT' => $this->GetPost('dirobject', ''),
	'SELOBJECTS' => '',
	'CLIPDIR' => $this->GetPost('clipdir', ''),
	'CLIPSEL' => $this->GetPost('clipsel', ''),
   	'CLIPFLAG'=> $this->GetPost('clipflag', '')));
	$this->v->CreateView();
	$this->setUI($this->v->GetView());

	}
	
	function Chmoding()
	{
  
	$this->v->UseTpl($_SERVER['DOCUMENT_ROOT'].$this->mod_path.'filechmoded.tpl');
	$this->v->SetVar('ACTION', 'listdir');	
	
	$perms = $_POST['newperms'];
	
	chmod($this->GetPost('clipsel', ''), $perms);
	
	$this->v->SetVars(array('PERMS' => $_POST['newperms'],    
	'DIROBJECT' => $this->GetPost('dirobject', ''),
	'SELOBJECTS' => '',
	'CLIPDIR' => $this->GetPost('clipdir', ''),
	'CLIPSEL' => $this->GetPost('clipsel', ''),
    'CLIPFLAG' => $this->GetPost('clipflag', '')));
	$this->v->CreateView();
	$this->setUI($this->v->GetView());

	}

	function NewFile()
	{
  	$this->v->UseTpl($_SERVER['DOCUMENT_ROOT'].$this->mod_path.'fileeditor.tpl');
	$this->v->SetVars(array('ACTION' => 'savefile',
'FILENAME'=> '',
'LINES'=> '',
'DIROBJECT'=> $this->GetPost('dirobject', ''),
'SELOBJECTS'=> '',
'CLIPDIR'=> $this->GetPost('clipdir', ''),
'CLIPSEL'=> $this->GetPost('clipsel', ''),
'CLIPFLAG'=> $this->GetPost('clipflag', '')));
	$this->v->CreateView();
	$this->setUI($this->v->GetView());

	}

	function SaveFile()
	{
               
    $filename = $this->GetPost('dirobject', $_SERVER['DOCUMENT_ROOT']).'/'.$_POST['filename'];
  
    $fh = fopen($filename, "w");
    fwrite($fh, $this->GetPost('lines', ''));
    fclose($fh);
    
 
	$this->v->UseTpl($_SERVER['DOCUMENT_ROOT'].$this->mod_path.'filesaved.tpl');
	$this->v->SetVars(array('ACTION' => 'listdir',
	'FILENAME'=>  $filename,
	'DIROBJECT'=>  $this->GetPost('dirobject', ''),
	'SELOBJECTS'=>  '',
	'CLIPDIR'=>  $this->GetPost('clipdir', ''),
	'CLIPSEL'=>  $this->GetPost('clipsel', ''),
   	'CLIPFLAG'=>  $this->GetPost('clipflag', '')));
	$this->v->CreateView();
	$this->setUI($this->v->GetView());

	}

	function doAction($anAction)
	{
	    $selection = $this->GetPost('selobjects', '');
	    $clipselection = $this->GetPost('clipsel', '');
		$clipdir = $this->GetPost('clipdir', '');
		$clipflag = $this->GetPost('clipflag', '');
		$dir = $this->GetPost('dirobject', '');
		switch ($anAction)
		{
			case 'listdir' : {$this->ListDir($this->getDirObject()); break;};
			case 'copy' : {$this->ListDir($this->getDirObject()); break;};
			case 'move' : {$this->ListDir($this->getDirObject()); break;};
    		case 'delete' : {$this->DeleteFiles(); break;};
    		case 'upload' : {$this->PrepareUpload(); break;};
    		case 'uploading' : {$this->Uploading(); break;};
    		case 'mkdir' : {$this->MkDirUI(); break;};
    		case 'newdir' : {$this->NewDir(); break;};
    		case 'paste' : {$this->Paste(); break;};
    		case 'editfile' : {$this->EditFile(); break;};
    		case 'newfile' : {$this->NewFile(); break;};
    		case 'savefile' : {$this->SaveFile(); break;};
			case 'chmod' : {$this->PrepareChmod(); break;};
			case 'rename' : {$this->PrepareRename(); break;};
			case 'chmoding' : {$this->Chmoding(); break;};
			case 'renaming' : {$this->Renaming(); break;};
                        case 'tounzip' : {$this->PrepareUnzip(); break;};
			case 'unzip' : {$this->Unzip(); break;};
		}
	}
	function Run()
	{
 		 $this->setParams(); // Установить параметры
		 $this->doAction($this->getAction()); // Выполнить действие
		 return $this->getUI(); // Вернуть интерфейс
	}

	function TestRun()
	{
		$this->setAction('listdir');
		$this->setDirObject('plugins');
	    $this->doAction($this->getAction());
	    return $this->getUI();
	}

	function getAction()
	{
		return $this->action;
	}
	function setAction($anAction)
	{
		$this->action = $anAction;
	}
	function getDirObject()
	{
		return $this->dirobject;
	}
	function setDirObject($anObject)
	{
		$this->dirobject = $anObject;
	}

}

?>