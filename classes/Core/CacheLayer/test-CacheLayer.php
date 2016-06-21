<?php
define('APP', 0);
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/CacheLayer/CacheLayer.class.php';
$v = new CacheLayer($vars);
if ($v->failed('number')) // Кэш устарел или не существует
 {
    echo 'Value of Number not found. Getting it from other source and saving it to cache<br/>';
    $number = rand(1, 10); // Получаем из базы и обновляем
	$v->save('number', $number);	
}
else
{
   echo 'Value of Number will be getted from cache<br/>';   
 $number = $v->get_from_cache('number');
};


if ($v->failed('params'))
 {
 echo 'Value of array Params not found. Getting it from other source and saving it to cache<br/>';
 $params = array('Wow'=>rand(1, 5));
 $v->save('params', $params);
 }
else
{
echo 'Value of Array will be getted from cache<br/>';   
 $params = $v->get_from_cache('params');
};

 echo 'Закэшированное число'.$number."<br/>";
 echo 'Закэшированный массив:'.$params['Wow']."<br/>";

?>

