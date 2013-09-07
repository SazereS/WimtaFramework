<?php

class Library_Controller extends Library_Base{

    protected $_vars = array();
    public  $view;

    public function init(){

    }

    public function __get($name) {
        return $this->_vars[$name];
    }

    public function __set($name, $value) {
        return $this->_vars[$name] = $value;
    }

}