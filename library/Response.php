<?php

namespace Library;

class Response{

    private $_content;

    /**
     * @var View\Layout
     */
    private $_layout;

    public function __construct() {
        $this->_layout = new View\Layout();
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

    public function renderLayout(View $view){
        $this->setContent(
                $this->_layout->render(
                        ($layout = Settings::getInstance()->default_layout)
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