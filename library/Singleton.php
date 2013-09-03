<?php

class Library_Singleton{

    private static $_instance = NULL;

    private function __construct(){

    }

    protected function __clone() {

    }

    public static function getInstance(){
        $class = get_called_class();
        if(is_null(self::$_instance)){
            self::$_instance = new $class;
        }
        return self::$_instance;
    }

}