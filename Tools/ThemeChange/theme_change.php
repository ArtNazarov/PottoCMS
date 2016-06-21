<?php
session_start();
$_SESSION['themeid'] = $_GET['themeid'];
/*
see /themes/themeid/theme.ini
varname = varvalue
*/
?>