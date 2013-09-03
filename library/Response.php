<?php

class Library_Response{

    private $_content;
    private $_layout;

    public function __construct() {
        $this->_layout = new Library_View_Layout();
    }

    public function writeContent(){
        echo $this->_content;
        return $this;
    }

    public function setContent($content){
        $this->_content = $content;
        return $this;
    }

    public function getContent(){
        return $this->_content;
    }

    public function getLayout(){
        return $this->_layout;
    }

    public function renderLayout(Library_View $view){
        $this->setContent(
                $this->_layout->render(
                        ($layout = Library_Settings::getInstance()->system['default_layout'])
                        ? $layout
                        : 'default',
                        array_merge(
                                $view->getOut(),
                                array('content' => $view->rendered)
                        )
                )->rendered
                );
        return $this;
    }

}