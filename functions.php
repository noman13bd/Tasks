<?php

/**
 * Sanitize data which will be injected into SQL query
 *
 * @param string $string SQL data
 * @param bool $htmlOK
 * @return string Sanitized data
 */
function pSQL($string, $htmlOK = false)
{
    return Db::getInstance()->escape($string, $htmlOK);
}

function bqSQL($string)
{
    return str_replace('`', '\`', pSQL($string));
}