<?php

namespace Library\Db\Table;

class Row implements \IteratorAggregate
{

    protected $_new = false;
    protected $_cells;
    protected $_belongs_to;
    protected $_has_many;
    protected $_joined = array('has_many' => array());

    /**
     *
     * @var \Library\Db\Table
     */
    protected $_table;
    protected $_id;

    public function __construct(\Library\Db\Table $table, array $cells = NULL)
    {
        $this->_table    = $table;
        $this->_id       = $cells[$this->getKeyField()];
        if (is_null($cells)) {
            $this->_cells = array();
            $this->_new   = true;
            /**
             * @todo Перенести эту хрень в Table
             */
            $res          = \Library\Db\Adapter::getInstance()
                ->query(
                    'SHOW COLUMNS FROM `'
                    . $this->_table->getTableName()
                    . '` WHERE `Field` = \'created_at\''
                );
            if ($res->rowCount() == 1) {
                $this->_cells['created_at'] = date('Y-m-d h:i:s');
            }
        } else {
            unset($cells[$this->getKeyField()]);
            $this->_cells = $cells;
        }
        foreach ($this->_table->getJoined() as $table => $has_many) {
            $this->_has_many[($has_many['as']) ? $has_many['as'] : $table] = $table;
        }

    }

    public function __set($name, $value)
    {
        if ($name == $this->getKeyField()) {
            throw new \Library\Db\Exception('Cannot change primary key value!');
        }
        $this->_cells[$name] = $value;
        return $this;
    }

    public function __get($name)
    {
        if ($name == $this->getKeyField()) {
            return $this->_id;
        } elseif(isset($this->_has_many[$name])){
            if(!$this->_joined['has_many'][$name]){
                $class = '\\Application\\Models\\'
                    . \Library\Base::getRealName($this->_has_many[$name]);
                $model = new $class();
                $has_many = $this->_table->getJoined();
                $this->_joined['has_many'][$name] = $model->fetchAll(
                    '`'
                    . $has_many[$this->_has_many[$name]]['public_key']
                    . '` = '
                    . \Library\Db\Adapter::getInstance()->quote($this->getKey())
                );
                return $this->_joined['has_many'][$name];
            }
        }
        return $this->_cells[$name];
    }

    public function getTableName()
    {
        return $this->_table->getTableName();
    }

    public function getKeyField()
    {
        return $this->_table->getKeyField();
    }

    public function getKey()
    {
        return $this->_id;
    }

    /**
     *
     * @return array
     */
    public function toArray()
    {
        return (array) array_merge(
            $this->_cells,
            ($this->_id)
            ? array($this->getKeyField() => $this->getKey())
            : array()
        );
    }

    public function save()
    {
        if ($this->_new) {
            try {
                $id = $this->_table->insertRow($this->_cells, false);
                $this->_new = false;
                $res        = \Library\Db\Adapter::getInstance()
                    ->find(
                        $this->getTableName(),
                        $id
                    );
                $cells        = $res->fetch(\PDO::FETCH_ASSOC);
                $this->_id    = $cells[$this->getKeyField()];
                unset($cells[$this->getKeyField()]);
                $this->_cells = $cells;
            } catch (\Library\Db\Exception $e) {
                throw new \Library\Db\Exception($e->getMessage());
                return false;
            }
        } else {
            $where = '`'
                . $this->getKeyField()
                . '` = '
                . \Library\Db\Adapter::getInstance()->quote($this->_id);
            if (isset($this->_cells['updated_at'])) {
                unset($this->_cells['updated_at']);
            }
            try {
                $this->_table->updateRow($where, $this->_cells);
            } catch (\Library\Db\Exception $e) {
                throw new \Library\Db\Exception($e->getMessage());
                return false;
            }
        }
        return $this;
    }

    public function delete()
    {
        $where = '`'
            . $this->getKeyField()
            . '` = '
            . \Library\Db\Adapter::getInstance()->quote($this->_id);
        try {
            $this->_table->deleteRows($where);
        } catch (\Library\Db\Exception $e) {
            throw new \Library\Db\Exception($e->getMessage());
            return false;
        }
        return true;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->_cells);
    }

}