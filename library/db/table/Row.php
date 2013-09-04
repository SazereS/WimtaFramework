<?php

class Library_Db_Table_Row implements IteratorAggregate{

    private $_cells;
    private $_statement;

    public function __construct($cells = NULL) {
        $this->_cells = $cells;
    }

    public function __set($name, $value) {
        $this->_cells[$name] = $value;
        return $this;
    }

    public function __get($name) {
        return $this->_cells[$name];
    }

    public function getIterator() {
        return new ArrayIterator($this->_cells);
    }

}