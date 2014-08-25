<?php

namespace Library;

class Registry extends Singleton
{

    protected $_data;

    protected function __construct()
    {
        parent::__construct();
        Base::registerHelper('getRegistry', function(){ return \Library\Registry::getInstance(); });
    }

    public function __set($name, $value)
    {
        return $this->set($name, $value);
    }

    public function set($name, $value)
    {
        $this->_data[$name] = $value;
        return $this;
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function get($name)
    {
        return $this->_data[$name];
    }

}