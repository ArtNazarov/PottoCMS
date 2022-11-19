<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core/FileUtils/FileUtils.class.php';
class Log extends FileUtils
{            
    var $ignore = array();
    function __construct(array $params)
{
        // echo "Вход в Log->__construct()<br/>"; 
         parent::__construct($params);
         //echo "Выход из Log->__construct()<br/>";
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
            $this->WriteFile(
                    array(
                        'filename' => $real_path,
                        'message' => $message,
                        'mode' =>
                            array(
                                'with_date' => true,
                                'rw' => 'a+')
                            ));
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