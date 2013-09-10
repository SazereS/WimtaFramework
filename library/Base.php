<?php

namespace Library;

class Base{

    public function __call($name, $arguments) {
        $class_name = '\\Helpers\\'.$name;
        return call_user_func_array($class_name . '::' . $name, $arguments);
    }

}