<?php

namespace Library;

class Exception extends \Exception
{

    public function __construct($string)
    {
        StackTrace::getInstance()->construct($string);
        parent::__construct($string);
    }

}