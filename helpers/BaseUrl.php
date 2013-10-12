<?php

namespace Helpers;

class BaseUrl
{

    public static function baseUrl($url)
    {
        $dir = trim(dirname($_SERVER['PHP_SELF']), '\\');
        return '/' . trim($dir . '/' . trim($url, '/'), '/');
    }

}