<?php
// Коллекция функций для работы с битовым числовым кодом

// Примечание - стандартные функции PHP
// decbin - Десятичное число в строку битов
// bindec - Битовую строку в десятичное число
// hexdec, dechec - 16-ичные числа
// octdec, decoct - 8-ичный числа
// baseval - между системами с основанием счисления от 2 до 36

// Переключение бита
function bit_toggle($number, $bit_position) 
{
  return ($number ^ (1 << $bit_position));
}
// Включение бита
function bit_on($number, $bit_position)
{
return ((1 << $bit_position) | $number);
}
// Выключение бита
function bit_off($number, $bit_position)
{
 return ($number & ~( 1 << $bit_position ));
}
// Получить бит
function bit_get($number, $bit_position)
{
return ($number >> $bit_position) % 2;
}
// Реверс бита
function bit_reverse($number)
{
$r=0;
for ($i=0; $i<8; $i++) 
 { if (bit_get($number, $i)==1) 
    { $r = bit_on($r, 7-$i); } 
			else
    {$r=bit_off($r, 7-$i); };
 };
return $r;
}
// Инверсия битов
function bit_inverse($number)
{
return (~ $number);
}
function XORmask($n1, $n2)
{
return ($n1^$n2);
}
function ORmask($n1, $n2)
{
return ($n1 | $n2);
}
function ANDmask($n1, $n2)
{
return ($n1 & $n2);
}

// Переупорядочение битов
function bit_order($number, $n0, $n1, $n2, $n3, $n4, $n5, $n6, $n7)
{
$b_map = array(0,0,0,0, 0,0,0,0);
for ($i=0; $i<8; $i++)  { $b_map[$i]=bit_get($number, $i); };
$r = 0;
if ($b_map[$n0]==1) { $r=bit_on($r, 0); } else {$r=bit_off($r, 0);};
if ($b_map[$n1]==1) { $r=bit_on($r, 1); } else {$r=bit_off($r, 1); };
if ($b_map[$n2]==1)  {$r=bit_on($r, 2); } else {$r=bit_off($r, 2);};
if ($b_map[$n3]==1)  {$r=bit_on($r, 3); } else {$r=bit_off($r, 3);};

if ($b_map[$n4]==1)  { $r=bit_on($r, 4); } else {$r=bit_off($r, 4);};
if ($b_map[$n5]==1)  { $r=bit_on($r, 5); } else {$r=bit_off($r, 5);};
if ($b_map[$n6]==1) { $r=bit_on($r, 6); } else {$r=bit_off($r, 6);};
if ($b_map[$n7]==1)  { $r=bit_on($r, 7); } else {$r=bit_off($r, 7);};
return $r;
}


?>