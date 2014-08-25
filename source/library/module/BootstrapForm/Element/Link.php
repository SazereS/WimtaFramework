<?php

namespace Library\Module\BootstrapForm\Element;

class Link extends \Library\Module\BootstrapForm\Element
{

    protected $_template      = '<a %1$s>%2$s</a>';
    protected $_classes       = 'btn btn-link';

    public function __construct($name)
    {
        parent::__construct($name);
    }

}
