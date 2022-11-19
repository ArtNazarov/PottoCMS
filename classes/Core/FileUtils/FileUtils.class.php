<?php

class FileUtils
{
      function __construct(array $params)
      {
                   //echo "Вход в FileUtils->__construct()<br/>";  
                   //echo "Выход из FileUtils->__construct()<br/>"; 
      }
      function DoubleDots($path)
      {
       $test = false;
       if (strpos($path, '..')!==false) {$test = true;};
       return $test;
      }
      
      function StopOnDots($path)
      {
          if ($this->DoubleDots($path))
          {
              die("Error: DOUBLE DOTS IN $path");
          };
      }
      
   
      function Now()
        {
            $str = date("j, n, Y H:i:s") . ' :: ';
            return $str;
        }  
      // Пишет файл
      function WriteFile($params)
        {                       
        $aFileName = $params['filename'];
        $mode = $params['mode'];
        $message = $params['message'];
        $this->StopOnDots($aFileName);
        
        $qfilename = $_SERVER['DOCUMENT_ROOT'].$aFileName;
        
        $notExisted = !file_exists( $qfilename );
        
     
                
        $fh = @fopen($qfilename, $mode['rw']);
	flock($fh, LOCK_EX);
        
	if ($mode['with_date'])
        {	
        fwrite($fh, $this->Now() . " $message \n");
        }
        else
        {
        fwrite($fh, $message);
        };
        flock($fh, LOCK_UN);
	fclose($fh);
        
        }
      // Затирает файл, но не удаляет его  
        function ClearFile($aFileName)
        {
                $this->StopOnDots($aFileName);
            	$fh = fopen($_SERVER['DOCUMENT_ROOT'].$aFileName, 'w+');
                fwrite($fh, '');
		fclose($fh);
        }
      // Считывает файл
        function ReadFile($aFileName)
	{
        $this->StopOnDots($aFileName);            
        $fcontent = '';
        $fh = @fopen($_SERVER['DOCUMENT_ROOT']."$aFileName", "r");
        if ($fh !== false)
        {
	$fcontent = fread($fh, filesize($_SERVER['DOCUMENT_ROOT'].$aFileName));        
	fclose($fh);
        };
       // var_dump($fcontent);
	return $fcontent;
	}
      // Получает размер директории
        function gds($dir_name)
{
        $this->StopOnDots($dir_name);     
        $dir_size =0;
           if (is_dir($dir_name)) {
               if ($dh = opendir($dir_name)) {
                  while (($file = readdir($dh)) !== false) {
                        if($file !="." && $file != ".."){
                              if(is_file($dir_name."/".$file)){
                                   $dir_size += filesize($dir_name."/".$file);
                             }
                             /* check for any new directory inside this directory */
                             if(is_dir($dir_name."/".$file)){
                                $dir_size +=  $this->gds($dir_name."/".$file);
                              }
                           }
                     }
             }
       }
closedir($dh);
return $dir_size;
}

 // Полное удаление каталога
 function full_del_dir ($directory)
  {
  $this->StopOnDots($directory);     
  $dir = opendir($directory);
  while(($file = readdir($dir)))
  {
    if ( is_file ($directory."/".$file))
    {
      unlink ($directory."/".$file);
    }
    else if ( is_dir ($directory."/".$file) &&
             ($file != ".") && ($file != ".."))
    {
      $this->full_del_dir ($directory."/".$file);
    }
  }
  closedir ($dir);
  rmdir ($directory);
  }

// Копирует файлы и непустые каталоги
 function rcopy($src, $dst) {
  $this->StopOnDots($src);     
  $this->StopOnDots($dst);     
  if (file_exists($dst)) rmdir($dst);
  if (is_dir($src)) {
    mkdir($dst);
    $files = scandir($src);
    foreach ($files as $file)
    if ($file != "." && $file != "..") $this->rcopy("$src/$file", "$dst/$file");
  }
  else if (file_exists($src)) copy($src, $dst);
}

// Разархивирование
  function UnArch($ZipArchive, $ExtractFolder)
  {
    $this->StopOnDots($ExtractFolder);      
  $zip = new ZipArchive; 
  $zip->open($ZipArchive); 
  $zip->extractTo($ExtractFolder); 
  $zip->close(); 
  }
  /* Получить родительский каталог */
  function get_parent_dir($aDir)
  {
      $this->StopOnDots($aDir);
      $str = substr($aDir, 0, strrpos($aDir, '/'));
      if (strlen($str)<strlen($_SERVER['DOCUMENT_ROOT']))
      {
          $str = $_SERVER['DOCUMENT_ROOT'];
      };
      return $str;
  }
// Получить имена и другую информацию о файлах в каталоге
  function DirScan($aDir)
  {   
      $this->StopOnDots($aDir);
      $fs = array();      
      if (strrpos($aDir, '..')==(strlen($aDir)-2))
	      {
	      	$aDir = substr($aDir, 0, strrpos($aDir, '/')-1);
	      	$aDir = substr($aDir, 0, strrpos($aDir, '/'));
	      	if (strlen($aDir)<strlen($_SERVER['DOCUMENT_ROOT']))
	      	{$aDir=$_SERVER['DOCUMENT_ROOT'];};
	      };
              
		$h = '';		
		$file_list = scandir("$aDir");
		foreach ($file_list as $i=>$entry)
		{
			$entry_short = $entry;
			$jsid = str_replace(".", '', $entry_short);
			if ($entry_short!='.') {
			$entry_full = "$aDir/$entry";
                        if ($entry_short==='..') {
			$entry_full = $this->get_parent_dir($aDir);
                        
                        };
		   if (is_dir($entry_full)==true)
		    {
			   
	    	    $fs[] =     array('FILENAME'=>$entry_short,
				'FULLFILENAME'=>$entry_full,
				'FILEPERMS' => substr(sprintf('%o', fileperms($entry_full)), -4),
				'FILETYPE' => 'dir',
				'JSID' => $jsid,
				'FILESIZE' => 'Каталог');						  
                    };};
		};
   	    foreach ($file_list as $i=>$entry)
		{
			$entry_short = $entry;
			if ($entry_short!='.') {
			$entry_full = "$aDir/$entry";
			$jsid = str_replace(".", '', $entry_short);

		   if (is_dir($entry_full)==false)
		    {
			$fs[] = array('FILENAME' => $entry_short,
				'FULLFILENAME' => $entry_full,
  	    	   'FILETYPE' => 'file',
				'FILESIZE' => filesize($entry_full),
				'FILEPERMS' => substr(sprintf('%o', fileperms($entry_full)), -4),
				'ACTION_LINKS' => '',
				'JSID' => $jsid);			
			};};
		   };		
		
         return $fs;          
	}                    
}
?>
