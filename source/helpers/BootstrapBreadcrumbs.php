<?php

namespace Helpers;

class BootstrapBreadcrumbs
{

    public static $crumbs = array();

    static public function bootstrapBreadcrumbs($title = NULL, $link = '')
    {
        if(!is_null($title)){
            self::$crumbs[\Library\Base::baseUrl($link)] = $title;
            return true;
        } else {
            $lis = array();
            $crumbs = self::$crumbs;
            $last = array_pop($crumbs);
            foreach($crumbs as $link => $title){
                $lis[] = '<li><a href="' . $link . '">' . $title . '</a></li>';
            }
            $lis[] = '<li class="active">' . $last . '</li>';
            return '<ol class="breadcrumb">' . implode('', $lis) . '</ol>';
        }
    }
}