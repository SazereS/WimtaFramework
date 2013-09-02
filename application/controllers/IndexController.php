<?php

class Application_Controllers_IndexController extends Library_Controller{

    public function init(){
        $this->view->out = "Index: initialization\n";
    }

    public function indexAction(){
        $this->view->out .= "Index#index\n";
        $this->view->out .= "This is just zend clone =__=";
    }

}