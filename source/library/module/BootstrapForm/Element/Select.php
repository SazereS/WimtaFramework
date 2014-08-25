<?php

namespace Library\Module\BootstrapForm\Element;

class Select extends \Library\Module\BootstrapForm\Element
{

    protected $_template      = '<select %1$s>%2$s</select>';
    protected $_classes       = 'form-control';

    public function __construct($name)
    {
        parent::__construct($name);
    }

    public function render()
    {
        $value = $this->getValue();
        $options = $this->getAttribute('options');
        $this->removeAttribute('options');
        if(!is_array($options)){
            $options = array();
        }
        $vals = array();
        foreach ($options as $key => $val){
            $vals[] = '<option value="'
                . $key . '"'
                . (($value == $key) ? ' selected' : '')
                . '>'
                . $val
                . '</option>';
        }
        $this->setValue(implode("\n", $vals));
        return parent::render();
    }

}
