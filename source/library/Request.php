<?php

namespace Library;

class Request
{

    public $params = array();
    public $url    = '';

    public function __construct()
    {
        $dir_length = strlen(
            dirname($_SERVER['PHP_SELF'])
        );
        $this->url  = trim(
            substr($_SERVER['REQUEST_URI'], $dir_length),
            '/'
        );
    }

    public function getController()
    {
        return ($this->params['controller'])
            ? $this->params['controller']
            : (
                (Settings::getInstance()->default_controller)
                ? Settings::getInstance()->default_controller
                : 'index'
            );
    }

    public function setController($new_controller){
        $this->params['controller'] = strval($new_controller);
    }

    public function getAction()
    {
        return ($this->params['action'])
            ? $this->params['action']
            : (
                (Settings::getInstance()->default_action)
                ? Settings::getInstance()->default_action
                : 'index'
            );
    }

    public function setAction($new_action){
        $this->params['action'] = strval($new_action);
    }

}
