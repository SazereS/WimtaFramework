<?php

namespace Helpers;

class ObjectsToArray
{

    public static function objectsToArray($var)
    {
        if (is_array($var)) {
            $new = array();
            foreach ($var as $k => $v) {
                $new[$k] = self::objectsToArray($v);
            }
            $var = $new;
        } elseif (is_object($var)) {
            $vars = $var;
            $var  = array();
            foreach ($vars as $m => $v) {
                $var[$m] = self::objectsToArray($v);
            }
        }
        return $var;
    }

}