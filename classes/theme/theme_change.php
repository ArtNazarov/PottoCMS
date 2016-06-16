<?php
session_start();
$_SESSION['themeid'] = $_GET['themeid'];
/*
Файл с настройками темы должен быть одноименным
и храниться в каталоге /themes/themeid/theme.ini
Формат файла темы
varname = varvalue
*/
?>