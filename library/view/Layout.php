<?php

class Library_View_Layout extends Library_View{

    public function __construct($path = NULL, $out = array()) {
        parent::__construct(
                ($path != NULL)
                ? $path
                : APPLICATION_PATH . 'views' . DIRECTORY_SEPARATOR . 'layouts',
                $out
                );
    }

}