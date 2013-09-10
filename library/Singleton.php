<?php

namespace Library;

class Singleton{

    private static $_instances;

    protected function __construct() {

    }

    private function __clone() {

    }

    public static function getInstance() {
        $class = get_called_class();
        if (!isset(self::$_instances[$class])) {
            self::$_instances[$class] = new $class;
        }
        return self::$_instances[$class];
    }

}