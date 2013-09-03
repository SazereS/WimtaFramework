<?php

class Library_Response{

    private $_content;
    private $_layout = 'default';

    public function writeContent(){
        echo $content;
        return $this;
    }

    public function setContent($content){
        $this->_content = $content;
        return $this;
    }

    public function getContent(){
        return $this->_content;
    }

}