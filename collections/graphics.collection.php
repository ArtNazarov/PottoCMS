<?php
  
function pxc($x,  $start, $scale)
{
return ($x-$start)*$scale;
}

function w_graphic($graphic, $startX, $endX, $startY, $endY)
{
$name = $graphic["name"]; // Название графика
$width = $graphic["width"]; // Ширина рисунка
$height = $graphic["height"]; // Высота рисунка
$points = $graphic["points"]; // Число точек на ломаной каждого графика
$lines = $graphic["lines"]; // Число графиков
$bgcolor = $graphic["bgcolor"]; // Цвет фона рисунка
$image = imagecreatetruecolor($width,$height); // Создаем картинку в памяти
imagefill($image, 0, 0, $bgcolor); // Заливаем фоновым цветом
imagestring($image, 3, $width / 2 - strlen($name), 10, $name, 0x000000); // Выводим имя графика вверху по центру
$scaleX = $width/($endX-$startX); // Маcштаб по X
$scaleY = $height/($endY-$startY); // Масштаб по Y 

for ($g=1;$g<=$lines;$g++) // По каждому графику
{
    
	$gr = "gr$g"; // Ключ массива
    $xd = $graphic["$gr"]["x"];	 // Асбсциссы
	$yd = $graphic["$gr"]["y"]; // Ординаты
	imagestring($image, 2, $width-50, $height-$g*14, $graphic[$gr]["caption"], $graphic[$gr]["fgcolor"]);
	// Выводим легенду графика справа
	for ($p=0;$p<$points;$p++) // По каждой ординате
	{
	$x = $xd[$p]; // Получили ординату
	imageline($image, pxc($x, $startX, $scaleX), 0, pxc($x, $startX, $scaleX), $height, 0xEEEEEE);
	// Нарисовали вертикальную линию
	imagestring($image, 3, pxc($x, $startX, $scaleX), $height-15, $xd[$p], 0x000000);
	// Подписываем эту линию значением ординаты
	};
	
	
	for ($p=0;$p<$points;$p++) // По каждой точке
	{
	// Получаем координаты очередного отрезка ломаной
    
	$x1 = $xd[$p-1];
    $y1 = $yd[$p-1];
	
	$x2 = $xd[$p];
    $y2 = $yd[$p];
	$fgcolor = $graphic[$gr]["fgcolor"]; // Цвет линии графика		
	
	// Выводим отрезок
	if (($p-1)!=-1) imageline( $image, pxc($x1, $startX, $scaleX), 
						$height-pxc($y1, $startY, $scaleY), 
						pxc($x2, $startX, $scaleX),  
						$height-pxc($y2, $startY, $scaleY), 
			$fgcolor);
	
	// Подписываем вершину ломаной значением ординаты
	imagestring($image, 2, pxc($x2, $startX, $scaleX)-7, 
	$height-pxc($y2, $startY, $scaleY), $yd[$p], 0xe8e8e8);
	
	};
};

 
  // Посылаем заголовок браузеру
  header('Content-type: image/png'); 
  // Посылаем биты изображения в формате PNG
  imagepng($image);
  // Удаляем рисунок из памяти
  imagedestroy($image);              
};
?>