<?php

class Library_Registry extends Library_Singleton{

    protected $_data;

    public function __set($name, $value) {
        return $this->set($name, $value);
    }

    public function set($name, $value) {
        $this->_data[$name] = $value;
        return $this;
    }

    public function __get($name) {
        return $this->get($name);
    }

    public function get($name) {
        return $this->_data[$name];
    }

}