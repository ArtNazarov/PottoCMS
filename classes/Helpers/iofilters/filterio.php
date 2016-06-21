<?php

function filter_array($arr, $maxlength)
{
if (true == isset($arr))
{
foreach ($arr as $p => $v)
{
    if (strlen($v)<=$max_length)
$arr[$p] = trim(htmlspecialchars(addslashes(strip_tags($v))));
    else
 {$arr[$p] = "";};
};
};
};

function filter_get()
{
    filter_array($_GET, 1024);
}

function filter_post()
{
    filter_array($_POST, 1024);
}

function filter_both()
{
    filter_get();
    filter_post();
}

?>
