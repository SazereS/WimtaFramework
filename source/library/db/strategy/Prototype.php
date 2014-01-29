<?php

namespace Library\Db\Strategy;

class Prototype extends \PDO
{

    protected $_prepared = array();
    protected $_keys     = array();

    public function __construct(
        $dsn,
        $username = NULL,
        $passwd   = NULL,
        $options  = NULL
    )
    {
        try {
            parent::__construct($dsn, $username, $passwd, $options);
        } catch (\PDOException $e) {
            throw new \Library\Db\Exception($e->getMessage());
        }
    }

    public function fetchAll($table, $where = NULL, $order = NULL, $limit = NULL)
    {
        $where = ($where) ? ' WHERE ' . $where : '';
        $order = ($order) ? ' ORDER BY ' . $order : '';
        $limit = ($limit) ? ' LIMIT ' . $limit : '';
        $q     = 'SELECT * FROM `' . $table . '`' . $where . $order . $limit;
        return $this->query($q);
    }

    public function fetchRow($table, $where = NULL, $order = NULL)
    {
        return $this->fetchAll($table, $where, $order, 1);
    }

    public function getKeyField($table)
    {
        if (!$this->_keys[$table]) {
            $query               = $this->query(
                'SHOW COLUMNS FROM `' . $table . '` WHERE `key`=\'pri\''
            );
            $res                 = $query->fetchAll();
            $this->_keys[$table] = $res[0]['Field'];
        }
        return $this->_keys[$table];
    }

    public function find($table, $id)
    {
        $field = $this->getKeyField($table);
        $q     = 'SELECT * FROM `' . $table . '` WHERE `' . $field . '` = ?';
        if (!$this->_prepared[$q]) {
            $this->_prepared[$q] = $this->prepare($q);
        }
        try {
            $this->_prepared[$q]->execute(array($id));
            return $this->_prepared[$q];
        } catch (\PDOException $e) {
            throw new \Library\Db\Exception($e->getMessage());
        }
    }

    public function rowCount($table, $where = NULL, $sort = NULL, $limit = NULL)
    {
        $where   = ($where) ? ' WHERE ' . $where : '';
        $sort    = ($sort) ? ' ORDER BY ' . $sort : '';
        $limit   = ($limit) ? ' LIMIT ' . $limit : '';
        $q       = 'SELECT COUNT(*) FROM `' . $table . '`' . $where . $sort . $limit;
        $res     = $this->query($q);
        $fetched = $res->fetch();
        return $fetched[0];
    }

    public function insertRow($table, $values = array())
    {
        $anchors = $cols    = array();
        foreach ($values as $k => $v) {
            $cols   [] = $k;
            $anchors[] = ':' . $k;
        }
        $q = 'INSERT INTO `'
            . $table
            . '` (`'
            . implode('`, `', $cols)
            . '`) VALUES ('
            . implode(', ', $anchors)
            . ')';
        if (!$this->_prepared[$q]) {
            $this->_prepared[$q] = $this->prepare($q);
        }
        try {
            $this->_prepared[$q]->execute($values);
            return $this->lastInsertId();
        } catch (\PDOException $e) {
            throw new \Library\Db\Exception($e->getMessage());
        }
    }

    public function updateRow($table, $where = NULL, $values = array())
    {
        $where = ($where) ? ' WHERE ' . $where : '';
        $vals  = array();
        foreach ($values as $k => $v) {
            $vals[] = '`' . $k . '`' . '= :' . $k;
        }
        $q = 'UPDATE ' . $table . ' SET ' . implode(', ', $vals) . $where;
        if (!$this->_prepared[$q]) {
            $this->_prepared[$q] = $this->prepare($q);
        }
        try {
            $this->_prepared[$q]->execute($values);
            return $this->_prepared[$q];
        } catch (\PDOException $e) {
            throw new \Library\Db\Exception($e->getMessage());
        }
    }

    public function deleteRows($table, $where = NULL)
    {
        $where = ($where) ? ' WHERE ' . $where : '';
        return $this->exec('DELETE FROM `' . $table . '`' . $where);
    }

}