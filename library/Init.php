<?php

namespace Library;

class Init extends Base{

    public function init(){
        $methods = get_class_methods(get_called_class());
        foreach ($methods as $method){
            if('_init' == substr($method, 0, 5)){
                $this->$method();
            }
        }
        return $this;
    }

    public function preInit(){

    }

    public function postInit(){

    }

}