<?php

namespace Helpers;

class IsPost
{

    public static function isPost()
    {
        if (empty($_POST)) {
            return false;
        }
        return true;
    }

}