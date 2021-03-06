<?php

namespace Library\Db;

class Table
{

    protected $_table;

    /**
     *
     * @var \Library\Db\Table\Row
     */
    protected $_current = NULL;
    protected $_belongs_to      = array();
    protected $_has_many        = array();

    public $joined_data = array();

    public function __construct()
    {
        //$this->_table = $table;
    }

    public function __toString()
    {
        return $this->getTableName();
    }

    public function getTableName()
    {
        return $this->_table;
    }

    public function getCurrent()
    {
        return $this->_current;
    }

    public function getJoined($type = 'has_many')
    {
        if ($type == 'belongs_to') {
            return $this->_belongs_to;
        } else {
            return $this->_has_many;
        }
    }

    /**
     *
     * @param null $where
     * @param null $order
     * @param null $limit
     * @return Row[]
     */
    public function fetchAll($where = NULL, $order = NULL, $limit = NULL)
    {
        $temp = Adapter::getInstance()
            ->fetchAll($this->getTableName(), $where, $order, $limit);
        $rows = array();
        while ($data = $temp->fetch(\PDO::FETCH_ASSOC)) {
            $rows[] = new Table\Row($this, $data);
        }
        return $rows;
    }

    /**
     *
     * @return \Library\Db\Table\Row
     */
    public function fetchRow($where = NULL, $order = NULL)
    {
        $temp           = Adapter::getInstance()->fetchRow($this->getTableName(), $where, $order);
        $data           = $temp->fetch(\PDO::FETCH_ASSOC);
        if(!$data){
            return false;
        }
        return $this->_current = new Table\Row($this, $data);
    }

    public function getKeyField()
    {
        return Adapter::getInstance()->getKeyField($this->getTableName());
    }

    /**
     *
     * @return \Library\Db\Table\Row
     */
    public function find($id)
    {
        $temp = Adapter::getInstance()->find($this->getTableName(), $id);
        if ($temp->rowCount() > 0) {
            $data           = $temp->fetch(\PDO::FETCH_ASSOC);
            return $this->_current = new Table\Row($this, $data);
        } else {
            return false;
        }
    }

    /**
     *
     * @return integer
     */
    public function rowCount($where = NULL, $sort = NULL, $limit = NULL)
    {
        return Adapter::getInstance()->rowCount($this->_table, $where, $sort, $limit);
    }

    /**
     *
     * @return \Library\Db\Table\Row
     */
    public function insertRow($values = array(), $return_inserted = true)
    {
        $res = Adapter::getInstance()
            ->query(
                'SHOW COLUMNS FROM `'
                . $this->getTableName()
                . '` WHERE `Field` = \'created_at\''
            );
        if ($res->rowCount() == 1 AND !isset($values['created_at'])) {
            $values['created_at'] = date('Y-m-d H:i:s');
        }
        $id = Adapter::getInstance()->insertRow($this->getTableName(), $values);
        if ($return_inserted) {
            return $this->find($id);
        }
        return $id;
    }

    /**
     *
     * @return \Library\Db\Table\Row
     */
    public function newRow()
    {
        return $this->_current = new Table\Row($this);
    }

    /**
     *
     * @return integer
     */
    public function updateRow($where = NULL, $values = array())
    {
        $temp = Adapter::getInstance()->updateRow($this->getTableName(), $where, $values);
        return $temp->rowCount();
    }

    public function deleteRows($where = NULL)
    {
        return Adapter::getInstance()->deleteRows($this->getTableName(), $where);
    }

    /**
     *
     * @param string $table
     * @param string $public_key
     * @param string $as
     * @return \Library\Db\Table
     */
    public function belongsTo($table, $public_key, $as = false)
    {
        $this->_belongs_to[$table] = array(
            'public_key' => (string) $public_key
        );
        if($as){
            $this->_belongs_to[$table]['as'] = (string) $as;
        }
        return $this;
    }

    /**
     *
     * @param string $table
     * @param string $public_key
     * @param string $as
     * @return \Library\Db\Table
     */
    public function hasMany($table, $public_key, $as = false)
    {
        $this->_has_many[$table] = array(
            'public_key' => (string) $public_key
        );
        if ($as) {
            $this->_has_many[$table]['as'] = (string) $as;
        }
        return $this;
    }

}