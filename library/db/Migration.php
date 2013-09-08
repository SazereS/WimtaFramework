<?php

class Library_Db_Migration{

    public $version = '';

    public function apply() {

    }

    public function rollback() {

    }

    public function __call($name, $arguments) {
        $c = Library_Db_Adapter::getInstance();
        return call_user_func_array(array($c, $name), $arguments);
    }
}