<?php

class Library_Db_Adapter extends Library_Singleton{

    /**
     *
     * @var Library_Db_Strategy_Prototype
     */
    private $_connection;

    public function setStrategy(Library_Db_Strategy_Prototype $strategy) {
        $this->_connection = $strategy;
        $this->_connection->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        return $this;
    }

    public function __call($name, $arguments) {
        $c = $this->_connection;
        return call_user_func_array(array($c, $name), $arguments);
    }

}