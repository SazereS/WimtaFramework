<?php

class Library_Db_Strategy_Prototype extends PDO{


    private $_prepared = array();
    private $_keys = array();

    public function __construct($dsn, $username = NULL, $passwd = NULL, $options = NULL) {
        try{
            parent::__construct($dsn, $username, $passwd, $options);
        }catch(PDOException $e){
            throw new Library_Db_Exception($e->getMessage());
        }
    }

    public function fetchAll($table, $where = NULL, $order = NULL, $limit = NULL){
        $where = ($where) ? ' WHERE ' . $where : '';
        $sort  = ($sort) ? ' ORDER BY ' . $sort : '';
        $limit = ($limit) ? ' LIMIT ' . $limit : '';
        $q     = 'SELECT * FROM `' . $table . '`' . $where . $sort . $limit;
        return $this->query($q);
    }

    public function fetchRow($table, $where = NULL, $order = NULL){
        return $this->fetchAll($table, $where, $order);
    }

    public function find($table, $id){
        if(!$this->_keys[$table]){
            $query = $this->query('SHOW COLUMNS FROM `' . $table . '` WHERE `key`=\'pri\'');
            $res = $query->fetchAll();
            $this->_keys[$table] = $res[0]['Field'];
        }
        $field = $this->_keys[$table];
        $q = 'SELECT * FROM `' . $table . '` WHERE `' . $field . '` = ?';
        if(!$this->_prepared[$q]){
            $this->_prepared[$q] = $this->prepare($q);
        }
        $this->_prepared[$q]->execute(array($id));
        return new Library_Db_Table_Row($this->_prepared[$q]);
    }

    public function countRows(){

    }

    public function deleteRows(){

    }

}