<?php

class Helpers_Redirect{
    public static function redirect($to){
        header('Location: ' . Helpers_BaseUrl::baseUrl($to));
        die(0);
    }
}