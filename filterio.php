<?php
if (true == isset($_GET))
{
foreach ($_GET as $p => $v)
{
$_GET[$p] = trim(htmlspecialchars(addslashes(strip_tags($v))));
};
};
if (true == isset($_POST))
{
foreach ($_POST as $p => $v)
{
    if (strlen($v)<=255)
        {
        $_POST[$p] = trim(htmlspecialchars(addslashes(strip_tags($v))));
        };
};
};
$rq = $_SERVER['REQUEST_URI'];
if (true == isset($_SESSION['hits']))
{
$_SESSION['hits'] = $_SESSION['hits'] + 1;
$mt = microtime(true);
$lasthit = ($mt-$_SESSION['lasthit']);
$_SESSION['lasthit'] = $mt;
$hits = $_SESSION['hits'];
}
else
{
$_SESSION['hits'] = 1;    
$hits = 1;
$_SESSION['lasthit'] = microtime(true);
$lasthit = 999;
};
$lasthit = $lasthit*1000;
$timeout = 15;
/*
if (($lasthit<=750) && ($rq!='/shop/view'))
        {            
            echo "<html><head>
            <meta http-equiv='refresh' content='$timeout;$rq'>
            <title>Система защиты</title><meta charset='utf-8'><body>
    <b>Система защиты.</b> 
    <p>Между запросами прошло $lasthit мс. Вы будете автоматически перенаправлены на запрошенную страницу
    через $timeout сек.</p>    
    </body></html>";
            exit;
            die();
        };
*/
?>
