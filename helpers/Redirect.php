<?php

namespace Helpers;

class Redirect{
    public static function redirect($to){
        header('Location: ' . Helpers_BaseUrl::baseUrl($to));
        die(0);
    }
}