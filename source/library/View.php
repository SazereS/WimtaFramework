<?php

namespace Library;

class View extends Base
{

    protected $_out     = array();
    protected $_path    = '';

    public $rendered = '';

    public function __construct($path = NULL, $out = array())
    {
        $this->_path = ($path != NULL)
            ? $path
            : APPLICATION_PATH . 'views' . DIRECTORY_SEPARATOR . 'custom';
        $this->_out  = array_merge($this->_out, $out);
    }

    public function __get($name)
    {
        return $this->_out[$name];
    }

    public function __set($name, $value)
    {
        return $this->_out[$name] = $value;
    }

    public function getOut()
    {
        return $this->_out;
    }

    public function render($file, $out = array())
    {
        $path     = $this->_path . DIRECTORY_SEPARATOR . $file . '.phtml';
        $response = $this->getRegistry()->response;
        if (!file_exists($path) AND ($response->getFormat() == $response::FORMAT_HTML)) {
            throw new View\Exception('Cannot find view file: "' . $path . '"');
        }
        extract(array_merge($out, $this->_out), EXTR_OVERWRITE);
        ob_start();
        if ($response->getFormat() == $response::FORMAT_HTML) {
            require($path);
        }
        $this->rendered = ob_get_contents();
        ob_end_clean();
        return $this;
    }

}