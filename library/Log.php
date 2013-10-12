<?php

namespace Library;

class Log
{

    private $_log = array();

    public function __construct()
    {

    }

    public function write($message)
    {
        $this->_log[] = '<span style="padding: .2em .6em .3em;">'
            . $message
            . '</span>';
        return $this;
    }

    public function writeWarning($message)
    {
        $this->_log[] = '<span style="background-color: #f0ad4e; color: white; padding: .2em .6em .3em;">'
            . '<b><span class="glyphicon glyphicon-warning-sign"></span> Warning! </b>'
            . $message
            . '</span>';
        return $this;
    }

    public function writeError($message)
    {
        $this->_log[] = '<span style="background-color: #d9534f; color: white; padding: .2em .6em .3em;">'
            . '<b><span class="glyphicon glyphicon-ban-circle"></span> Error! </b>'
            . $message
            . '</span>';
        return $this;
    }

    public function writeSuccess($message)
    {
        $this->_log[] = '<span style="background-color: #5cb85c; color: white; padding: .2em .6em .3em;">'
            . '<b><span class="glyphicon glyphicon-ok-circle"></span> Success! </b>'
            . $message
            . '</span>';
        return $this;
    }

    public function show()
    {
        echo '<pre style="width: 100%; line-height: 25px; font-size: 15px;">',
            implode(PHP_EOL, $this->_log),
            '</pre>';
        return $this;
    }

}