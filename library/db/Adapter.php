<?php

namespace Library\Db;

class Adapter extends \Library\Singleton{

    /**
     *
     * @var \Library\Db\Strategy\Prototype
     */
    private $_connection;

    /**
     * @return Strategy\Prototype;
     */
    public static function getInstance(){
        return parent::getInstance();
    }

    public function setStrategy(\Library\Db\Strategy\Prototype $strategy) {
        $this->_connection = $strategy;
        $this->_connection->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
        return $this;
    }

    public function __call($name, $arguments) {
        $c = $this->_connection;
        return call_user_func_array(array($c, $name), $arguments);
    }

}