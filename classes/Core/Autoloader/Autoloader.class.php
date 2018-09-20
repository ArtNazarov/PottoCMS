<?php

  

function getDirContents($dir, &$results = array()){
    $files = scandir($dir);

    foreach($files as $key => $value){
        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
        if(!is_dir($path)) {
            $results[] = $path;
        } else if($value != "." && $value != "..") {
            getDirContents($path, $results);
            $results[] = $path;
        }
    }

    return $results;
}
    
    function filter_files($dir){
        $result = [];
        
        for($i=0;$i<count($dir);$i++){
            if (strpos($dir[$i],'.class.php')!==FALSE){
                $classname = basename($dir[$i], '.class.php');
                $result[$classname] = $dir[$i];
            }
        }
        return $result;
    }

class Autoloader {
    function getAll(){
        $plugins = [];
        $dir = $_SERVER['DOCUMENT_ROOT'] . '\\classes\\';
        $files = getDirContents($dir);

        $plugins = filter_files($files);
        return $plugins;
    }
    function walk($do_require){
$files = $this->getAll();
$components = [];
$params = 0;
$k = 0;
foreach($files as $classname => $filename){
    
    echo 'Обработка '.$filename."<br/>";
    if ($do_require){
    require_once($filename);
    echo "req ok<br/>";
    try{
    echo "try to make obj $classname<br/>";    
    $components[$classname] = new $classname($params);
    echo "make obj ok<br/>";
    } catch (Exception $e){
        echo '!!! Выброшено исключение: ',  $e->getMessage(), "\n";
    }
    }
    echo "Создали экземпляр класса $classname<br/>";
    $k++;
    echo 'Выполнено ' . floor(100*$k/count($files)) . '%<br/>';
    $rp = floor($k/count($files)*80);
    echo  str_repeat('▓', $rp) . str_repeat('░', 80-$rp) . "<br/>"; 
   
}
echo "-- END WALK --";
    return $components;

    }
}