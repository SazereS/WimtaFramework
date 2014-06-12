<?php

namespace Library\Module\BootstrapForm\Element;

class Submit extends \Library\Module\BootstrapForm\Element
{

    protected $_template = '<button %1$s type="submit">%2$s</button>';
    protected $_classes = 'btn btn-default';

    public function __construct($name)
    {
        parent::__construct($name);
    }

    public function setValue($value)
    {
        if($this->_value){
            return $this;
        }

        $value = (string) $value;
        if($this->_value_protect){
            $value = addcslashes($value, $this->_value_protect);
        }
        $this->_value = $value;
        return $this;
    }

}
