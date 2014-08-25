<?php

namespace Library\Module\BootstrapForm\Element;

class Textarea extends \Library\Module\BootstrapForm\Element
{

    protected $_template = '<textarea %1$s>%2$s</textarea>';
    protected $_value_protect = false;
    protected $_classes = 'form-control';
    
    public function __construct($name)
    {
        parent::__construct($name);
    }

}

