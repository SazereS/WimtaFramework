<?php

class Library_Base{

    public function __call($name, $arguments) {
        $class_name = 'Helpers_'.$name;
        return call_user_func_array($class_name . '::' . $name, $arguments);
    }

}