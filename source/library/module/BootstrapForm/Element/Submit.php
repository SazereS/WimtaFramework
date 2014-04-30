<?php

namespace Library\Module\BootstrapForm\Element;

class Submit extends \Library\Module\BootstrapForm\Element
{

    protected $_template = '<button %1$s value="%2$s" type="submit">%2$s</button>';
    protected $_value_protect = '"';
    protected $_classes = 'btn btn-default';

    public function __construct($name)
    {
        parent::__construct($name);
    }

}
