<?php

namespace Helpers;

class XmlEncode
{

    public static function xmlEncode($array, $root = 'xmldata', $level = 0)
    {
        $res = '';
        if ($level == 0) {
            $res .= '<?xml version="1.0"?>' . "\n";
        }
        if ($root) {
            $res .= '<' . $root . '>' . "\n";
        }
        if ($array)
            foreach ($array as $k => $v) {
                $tag = $k;
                $params = '';
                if (is_int($k)) {
                    $tag = 'value';
                    $params = ' key = "' . $k . '"';
                }
                if (is_array($v)) {
                    $res .= str_repeat('  ', $level);
                    $res .= '<' . $tag . $params . '>' . "\n";
                    $res .= self::xmlEncode($v, false, $level + 1);
                    $res .= '</' . $tag . '>' . "\n";
                } else {
                    $res .= str_repeat('  ', $level);
                    $res .= '<' . $tag . $params . '>';
                    $res .= $v;
                    $res .= '</' . $tag . '>' . "\n";
                }
            }
        if ($root) {
            $res .= '</' . $root . '>';
        }
        return $res;
    }

}