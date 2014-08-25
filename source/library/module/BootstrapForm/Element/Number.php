<?php

namespace Library\Module\BootstrapForm\Element;

class Number extends \Library\Module\BootstrapForm\Element\Text
{

    protected $_template = '<input %1$s value="%2$s" type="number" />';

    public function __construct($name)
    {
        parent::__construct($name);
        $this->addValidator('int');
    }

}
