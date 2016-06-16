<?php
/*
 * Фильтры для входящих данных.
 * Перед использованием в модуле, сначала подключите в нем filterio.php
 */
/*
 * Получить email
 */
function IsEmail($var)
{
    return filter_var($var, FILTER_VALIDATE_EMAIL);
}

/*
 * Получить url
 */

function IsUrl($var)
{
    return filter_var($var, FILTER_VALIDATE_URL);
}

/*
 * Получить целое число
 */
function IsInt($var)
{
    return filter_var($var, FILTER_VALIDATE_INT);
}
/*
 * Получить вещественное число 
 */
function IsFloat($var)
{
    return filter_var($var, FILTER_VALIDATE_FLOAT);
}
?>