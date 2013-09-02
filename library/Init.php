<?php

class Library_Init extends Library_Base{

    public function init(){
        $methods = get_class_methods(get_called_class());
        foreach ($methods as $method){
            if('_init' == substr($method, 0, 5)){
                $this->$method();
            }
        }
    }

}