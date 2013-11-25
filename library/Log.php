<?php

namespace Library;

class Log
{

    private $_log = array();

    public function __construct()
    {

    }

    private function writeArray($array, $deep = 0, $key = false, $object = false){
        $this->writeHighlighted(
            str_repeat('    ', $deep)
            . (($key) ? '[' . $key . '] => ' : '')
            . (($object) ? get_class($object) : 'Array')
            . ' ('
        );
        foreach ($array as $key => $val){
            if (is_array($val)) {
                $this->writeArray($val, $deep + 1, $key);
            } elseif (is_object($val)) {
                $this->writeArray((array) $val, $deep + 1, $key, $val);
            } else {
                $this->write(str_repeat('    ', $deep + 1) . '[' . $key . '] => ' . $val);
            }
        }
        $this->write(
            str_repeat('    ', $deep)
            . ')'
        );
        return $this;
    }

    public function write($message)
    {
        if (is_array($message)) {
            $this->writeArray($message);
        } elseif (is_object($message)) {
            $this->writeArray((array) $message, 0, false, $message);
        } else {
            $this->_log[] = '<span style="padding: .2em .6em .3em;">'
                . (string) $message
                . '</span>';
        }
        return $this;
    }

    public function writeHighlighted($message){
        $this->_log[] = '<span style="background-color: #ccc; padding: .2em .6em .3em;">'
        . $message
        . '</span>';
        return $this;
    }

        public function writeWarning($message)
    {
        $this->_log[] = '<span style="background-color: #f0ad4e; color: white; padding: .2em .6em .3em;">'
            . '<b><span class="glyphicon glyphicon-warning-sign"></span> Warning! </b>'
            . strval($message)
            . '</span>';
        return $this;
    }

    public function writeError($message)
    {
        $this->_log[] = '<span style="background-color: #d9534f; color: white; padding: .2em .6em .3em;">'
            . '<b><span class="glyphicon glyphicon-ban-circle"></span> Error! </b>'
            . strval($message)
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