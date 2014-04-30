<?php

namespace Library\Module\BootstrapForm\Element;

class Text extends \Library\Module\BootstrapForm\Element
{

    protected $_template = '<input %1$s value="%2$s" type="text" />';
    protected $_value_protect = '"';
    protected $_classes = 'form-control';

    public function __construct($name)
    {
        parent::__construct($name);
    }

}
