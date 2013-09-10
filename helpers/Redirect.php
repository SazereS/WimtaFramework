<?php

namespace Helpers;

class Redirect{
    public static function redirect($to){
        header('Location: ' . \Helpers\BaseUrl::baseUrl($to));
        die(0);
    }
}