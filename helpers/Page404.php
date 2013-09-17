<?php

namespace Helpers;

class Page404
{

    public static function page404()
    {
        if($redirect = \Library\Settings::getInstance()->error_page404){
            \Helpers\Redirect::redirect($redirect);
        } else {
            \Helpers\Redirect::redirect('');
        }
    }

}