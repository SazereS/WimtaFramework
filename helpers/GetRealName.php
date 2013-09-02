<?php

class Helpers_GetRealName{
    public static function getRealName($name){
        $name = explode('-', $name);
        return implode('', $name);
    }
}