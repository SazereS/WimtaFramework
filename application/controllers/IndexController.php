<?php

class Application_Controllers_IndexController extends Library_Controller{

    public function init(){
        $this->view->out = "Index: initialization\n";
    }

    public function indexAction(){
        $this->view->out .= "Index#index\n";
        $this->view->out .= "This is just zend clone =__=";
    }

    public function formAction(){
        $texts = new Application_Models_Tests();
        if($this->isPost()){
            $post = $this->getPost();
            $texts->insertRow($post);
            $this->redirect('index/form');
        }
        $this->view->texts = $texts->fetchAll(NULL, 'id DESC');
    }

}