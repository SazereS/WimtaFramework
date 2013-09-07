<?php

class Library_Db_Table_Row implements IteratorAggregate{

    protected $_cells;
    protected $_table;
    protected $_id_field;
    protected $_id;

    public function __construct($table, array $cells = NULL) {
        $this->_table    = $table;
        $this->_id_field = Library_Db_Adapter::getInstance()->getKeyField($this->_table);
        $this->_id       = $cells[$this->_id_field];
        unset($cells[$this->_id_field]);
        $this->_cells = $cells;
    }

    public function __set($name, $value) {
        if($name == $this->_id_field){
            throw new Library_Db_Exception('Cannot change primary key value!');
        }
        $this->_cells[$name] = $value;
        return $this;
    }

    public function __get($name) {
        if($name == $this->_id_field){
            return $this->_id;
        }
        return $this->_cells[$name];
    }

    public function getKeyField(){
        return $this->_id_field;
    }

    public function getKey(){
        return $this->_id;
    }

    public function toArray(){
        return (array) $this->_cells;
    }

    public function save(){
        $where = '`' . $this->_id_field . '` = ' . Library_Db_Adapter::getInstance()->quote($this->_id);
        try{
            Library_Db_Adapter::getInstance()->updateRow($this->_table, $where, $values);
        }  catch (Library_Db_Exception $e){
            throw new Library_Db_Exception($e->getMessage());
            return false;
        }
        return true;
    }

    public function delete(){
        $where = '`' . $this->_id_field . '` = ' . Library_Db_Adapter::getInstance()->quote($this->_id);
        try{
            Library_Db_Adapter::getInstance()->deleteRow($this->_table, $where);
        }  catch (Library_Db_Exception $e){
            throw new Library_Db_Exception($e->getMessage());
            return false;
        }
        return true;
    }

    public function getIterator() {
        return new ArrayIterator($this->_cells);
    }

}