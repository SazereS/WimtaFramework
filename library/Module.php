<?php

namespace Library;

class Module
{

    public function __construct()
    {
        if(get_called_class() != 'Library\Module'){
            $module = explode('\\', get_called_class());
            Registry::getInstance()->log->write('> Module ' . end($module) . ' loaded!');
        }
    }

}