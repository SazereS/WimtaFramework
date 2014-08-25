<?php

namespace Library\View;

class Layout extends \Library\View
{

    public function __construct($path = NULL, $out = array())
    {
        parent::__construct(
            ($path != NULL)
            ? $path
            : APPLICATION_PATH
                . 'views'
                . DIRECTORY_SEPARATOR
                . 'layouts',
            $out
        );
    }

    public function renderCustom($file)
    {
        $view = new \Library\View();
        return $view->render($file, $this->_out)->rendered;
    }

}