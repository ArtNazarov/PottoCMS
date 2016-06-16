<?php
define('APP', 0);
session_start();

$root = $_SERVER['DOCUMENT_ROOT'];

require_once($root . '/classes/fileutils/fileutils.class.php');
require_once($root . '/classes/meerkat/meerkat.class.php');
$cnf = null;
$usr = new Meerkat($cnf);
$username = $usr->GetUsernameFromSession();
$usergroup = $usr->GetRole($username);
if ($usergroup=='admin')
{

function antivir($dir_name, $log, $util, &$file_arr)
{
           if (is_dir($dir_name)) {
               if ($dh = opendir($dir_name)) {
                  while (($file = readdir($dh)) !== false) {
                        if($file !="." && $file != ".."){
                              if(is_file($dir_name."/".$file)){
							    $md5 = md5_file($dir_name."/".$file);
								$long_str = 'FILENAME:' . $dir_name . '/' . $file . '|FILESIZE:' .  filesize($dir_name."/".$file) .  '|MD5:' . $md5 . "<br/>";
                                  $util->WriteFile($log,  $long_str);
								   echo $long_str;
								   $file_arr[$dir_name . '/' . $file] = $md5;
								   
                             }
                            
                             if(is_dir($dir_name."/".$file)){
                                antivir($dir_name."/".$file, $log, $util, &$file_arr);
                              }
                           }
                     }
             }
       }
closedir($dh);
}

$config = null;
$now = date("d.m.y");
$arr = array();
$log = '/logs/fs' .  $now  . '.log';
$slog = '/logs/sl' . $now . '.log';
$util = new FileUtils($config);
$util->ClearFile($log);
echo 'ROOT:' . $root . "<br/>";
echo 'DATE:' . $now . "<br/>";
echo 'LOG: '. $log . "<br/>";
echo 'SLOG: '. $slog . "<br/>";
antivir($root,  $log, $util, $arr);
$util->ClearFile($slog);
$util->WriteFile($slog, serialize($arr), false);
}
else
{
	echo 'Admin only';
};
?>