<?php

namespace Library;

class Controller extends Base
{

    protected $_vars = array();

    /**
     * @var \Library\View;
     */
    public $view;

    public function init()
    {

    }

    public function __get($name)
    {
        return $this->_vars[$name];
    }

    public function __set($name, $value)
    {
        return $this->_vars[$name] = $value;
    }

}