<?php

namespace Helpers;

class GetRealName{
    public static function getRealName($name){
        $name = explode('-', $name);
        return implode('', $name);
    }
}