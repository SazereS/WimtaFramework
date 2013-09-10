<?php

namespace Library;

class Controller extends \Library\Base{

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

    /**
     *
     * @return \Library\Request
     */
    public function getRequest(){
        return \Library\Registry::getInstance()->request;
    }

    /**
     *
     * @return \Library\Response
     */
    public function getResponse(){
        return \Library\Registry::getInstance()->response;
    }

    public function getParam($name) {
        return $this->getRequest()->params[$name];
    }
}