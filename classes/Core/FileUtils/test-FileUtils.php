<?php
define('APP', 0);
$params = null;
require_once $_SERVER['DOCUMENT_ROOT'].'classes/fileutils/fileutils.class.php';
$fs = new FileUtils($params);
$fs->WriteFile(
            array(                
            'filename' => 'test-file.txt', 
            'message' => 'test message',        
            'mode' =>
                array(
                    'with_date' => false,
                    'rw' => 'w+'
            )));
echo "<h1>Must be readed 'test message'</h1>";
$content = $fs->ReadFile('test-file.txt');
echo 'get:' . $content . "<br/>";
if ($content == 'test message' )
{
    echo "write-read:ok";
}
else
{
    echo "write-read:failed";
};

echo "<h1>Must be readed 'test message 2'</h1>";
$fs->WriteFile(
            array(                
            'filename' => 'test-file.txt', 
            'message' => 'test message',        
            'mode' =>
                array(
                    'with_date' => false,
                    'rw' => 'w+'
            )));
$content = $fs->ReadFile('test-file.txt');
echo 'get:' . $content . "<br/>";
if ($content == 'test message 2' )
{
    echo "rewrite-read:ok";
}
else
{
    echo "rewrite-read:failed";
};
// remove file
$fs->ClearFile('test-file.txt');
