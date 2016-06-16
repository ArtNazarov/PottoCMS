<?php
if (!defined('APP')) {die('ERROR');};
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/fileutils/fileutils.class.php';
class Duck extends FileUtils
{            
    var $ignore = array('cache', 'jerboa', 'sql');
    function __construct($params)
{
         parent::__construct($params);
}
        
        function GetPath($aFileName, $cat)
        {
            return '/'.$cat.'s/'.$aFileName.'.'.$cat;
        }
	function WriteLog($aFileName, $message)
	{
        $real_path = $this->GetPath($aFileName, 'log');
        if ( false == in_array($aFileName, $this->ignore))
        {            
            $this->WriteFile($real_path, $message);
        };
	}
	function ReadLog($aFileName)
	{
        $real_path = $this->GetPath($aFileName, 'log');
	$fcontent = $this->ReadFile($real_path);
	return $fcontent;
	}      
	function ClearLog($aFileName)
	{
	   $real_path = $this->GetPath($aFileName, 'log');
           $this->ClearFile($real_path);
	}
        function ClearDir($dir)
        {
        $dir = $_SERVER['DOCUMENT_ROOT'].$dir;
        $op_dir=@opendir($dir);
        while($file=@readdir($op_dir ))
        {
         if($file != "." && $file != "..")
         {
           @unlink ($dir.$file);
          }
}
        @closedir($op_dir);
        }
	function ClearLogs()
	{
            $dir = '/logs/';
            $this->ClearDir($dir);
	}
}

?>