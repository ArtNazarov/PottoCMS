<?php

if (!defined('APP')) {die('ERROR');};

/**
 * Разработчики:
 * Copyright (c) 2011-2012, Potto CMS - Artem Nazarov. All rights reserved.
 * Visit <a href="http://artnazarov.ru/aboutpottocms">Potto CMS Site</a> to find more information about the component.
 */

/**
 * \brief Простая капча.
 * Выводит изображение, которое должен распознать пользователь.
 * Ответ пользователя сверяется с правильным.
 * Требует наличия файла /classes/Services/CaptchaTool/image.php
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Core/ClassFactory/ClassFactory.class.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/classes/Helpers/Encryption/Encryption.class.php';


class CaptchaTool
{
   var $components;
   function __construct(array $params)
   {
	 $this->components['factory'] = new ClassFactory($params);
     // Настройки шаблонизатора
	 $this->components['view'] =  $this->components['factory']->createInstance("TemplateTool", $params, 'Core');	
	 
   }
   
   function __destruct()
   {
     foreach ($this->components as $key => $value)
	  {
		  unset($this->components[$key]);
	  };
	  unset($this->components);	  
   }

/**
 * \brief  Вопрос
 * Генерирует 2 случайных числа от 10 до 20, складывает их или вычитает
 * сохраняет результат в поле $_SESSION['question']
 * Выводит в картинку полученный пример
 */   
   
 function question()
{
$num1 = rand(10, 20); // генерируем
$num2 = rand(10, 20); // генерируем
$operation = rand(1, 2);
switch ($operation)
{
case 1 : { $result = $num1 + $num2; $qs = "$num1 plus $num2"; break; };
case 2 : { $result = $num1 - $num2; $qs = "$num1 minus $num2"; break; };
};
$_SESSION['question'] = $result; // запоминаем
$key = rand(9999, 99999);
$enc = new Encryption();
$qs = urlencode($enc->encrypt_data($key, $qs));
return "<img src='/classes/Services/CaptchaTool/image.php?text=$qs&key=$key'/> = ? "; // возвращаем в качестве результата
} 


/**
 * \brief  Простая проверка
 * Сверяет значение $_POST['answer'] с $_SESSION['question']
 * если равны, капча разгадана
 * @return bool
 */ 

function check() 
{
return ($_POST['answer']==$_SESSION['question']);
}

/**
 * \brief  Встраивает в форму капчу
 * Возвращает html фрагмент с изображением капчи
 * @return string
 */ 

function FormCaptcha()
{
 $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/classes/Services/CaptchaTool/captcha.tpl'); 
 $this->components['view']->SetVar('CAPTCHA', $this->question());
 $this->components['view']->CreateView();
 return $this->components['view']->GetView();
}

/**
 * Сообщение о неправильном ответе
 * @param $link ссылка на страницу, на которую можно вернуться, чтобы повторить попытку
 * @return string
 */ 

function msg_wrong_captcha($link)
{
	  $this->components['view']->UseTpl($_SERVER['DOCUMENT_ROOT'].'/templates/readers/actions/sys_message.tpl');
	  $this->components['view']->SetVar('SYS_TITLE', 'Сообщение');
	  $this->components['view']->SetVar('SYS_MESSAGE', 'Captcha неверна...');
	  $this->components['view']->SetVar('LINK_HREF', "$link");
	  $this->components['view']->SetVar('LINK_TITLE', 'вернуться к странице...');
	  $this->components['view']->CreateView();
	  $this->components['view']->Publish();
}

/**
 * Возвращает прямоугольную картинку со случайным фоновым цветом и пляшущими
 * буквами 
 * @param $w - ширина картинки
 * @param $h - высота картинки
 * @param $text - текст вопроса
 * @return image
 */

function captcha($w, $h, $text)
{

$image = imagecreatetruecolor($w,$h) // создаем изображение... 
    or die('Cannot create image');     // ...или прерываем работу скрипта в случае ошибки 


  // "Зальем" фон картинки случайным цветом...
  $bgcolor = rand(1, 0xFF)*0xFF+rand(1, 0xFF)+0xFF0000;
  imagefill($image, 0, 0, $bgcolor);
  
  $numlines = rand(3, 5);
  $px = 0;
  for ($i=1; $i<$numlines; $i++)
{
 $fgcolor = rand(1, 0xFF)*0xFF+rand(1, 0xFF)+0xFF0000;
 imageline($image, $px, rand(1, 3), $px, rand($h-3, $h), $fgcolor);
  $px = $px + 10 + rand(1, 3);
};
  $py = 0;
  for ($i=1; $i<$numlines; $i++)
{
 $fgcolor = rand(1, 0xFF)*0xFF+rand(1, 0xFF)+0xFF0000;
 imageline($image, rand(1, 3), $py,  rand($w-3, $w), $py, $fgcolor);
 $py = $py + 1 + rand(1, 3);
};




   $char_array = preg_split('//', $text, -1, PREG_SPLIT_NO_EMPTY);
   $px = 0;
   foreach($char_array as $char)
{
  $py = rand(1, 15); 
  $fgcolor = rand(0x000000, 0xFFFFFF);
  $fontsize = rand(2, 5);
  imagestring($image, $fontsize, $px, $py, $char, $fgcolor);
  $px = $px + 10 + rand(1, 3);
}

  // Устанавливаем тип документа - "изображение в формате PNG"...
  header('Content-type: image/png'); 
  // ...И, наконец, выведем сгенерированную картинку в формате PNG:
  imagepng($image);

  imagedestroy($image);                // освобождаем память, выделенную для изображения
}

}

 
?>