<?php

namespace Library\Db;

class Table{

    protected $_table;

    /**
     *
     * @var \Library\Db\Table\Row
     */
    protected $_current = NULL;

    public function __construct() {
        //$this->_table = $table;
    }

    public function getTableName(){
        return $this->_table;
    }

    public function getCurrent(){
        return $this->_current;
    }

    public function fetchAll($where = NULL, $order = NULL, $limit = NULL){
        $temp = Adapter::getInstance()->fetchAll($this->_table, $where, $order, $limit);
        $rows = array();
        while($data = $temp->fetch(\PDO::FETCH_ASSOC)){
            $rows[] = new Table\Row($this->_table, $data);
        }
        return $rows;
    }

    public function fetchRow($where = NULL, $order = NULL){
        $temp = Adapter::getInstance()->fetchRow($where, $order);
        $data = $temp->fetch(\PDO::FETCH_ASSOC);
        return $this->_current = new Table\Row($this->_table, $data);
    }

    public function getKeyField(){
        return Adapter::getInstance()->getKeyField($this->_table);
    }

    public function find($id){
        $temp = Adapter::getInstance()->find($this->_table, $id);
        if($temp->rowCount() > 0){
            $data = $temp->fetch(\PDO::FETCH_ASSOC);
            return $this->_current = new Table\Row($this->_table, $data);
        } else {
            return false;
        }
    }

    public function rowCount($where = NULL, $sort = NULL, $limit = NULL){
        return Adapter::getInstance()->rowCount($where, $sort, $limit);
    }

    public function insertRow($values = array(), $return_inserted = true){
        $res = Adapter::getInstance()->query('SHOW COLUMNS FROM `' . $this->_table . '` WHERE `Field` = \'created_at\'');
        if ($res->rowCount() == 1 AND !isset($values['created_at'])) {
            $values['created_at'] = date('Y-m-d h:i:s');
        }
        $id = Adapter::getInstance()->insertRow($this->_table, $values);
        if($return_inserted){
            return $this->find($id);
        }
        return $id;
    }

    /**
     *
     * @return \Library\Db\Table\Row
     */
    public function newRow() {
        return $this->_current = new Table\Row($this->_table);
    }

    public function updateRow($where = NULL, $values = array()){
        $temp = Adapter::getInstance()->updateRow($where, $values);
        return $temp->rowCount();
    }

    public function deleteRows($where = NULL){
        return Adapter::getInstance()->deleteRows($where);
    }

}