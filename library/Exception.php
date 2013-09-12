<?php

namespace Library;

class Exception extends \Exception
{

    public function __construct($string)
    {
        parent::__construct($string);
        $trace = new StackTrace($string);
        $trace->build()->show();
    }

}