<?php

namespace Library\Db;

use Library\Settings;

class Adapter extends \Library\Singleton
{

    /**
     *
     * @var \Library\Db\Strategy
     */
    private $_connection = null;

    /**
     * @return Strategy;
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    public function connect(){
        $strategy = false;
        if (Settings::getInstance()->db_driver == 'mysql') {
            $strategy = new Strategy\Mysql(
                Settings::getInstance()->db_mysql_host,
                Settings::getInstance()->db_mysql_dbname,
                Settings::getInstance()->db_mysql_login,
                Settings::getInstance()->db_mysql_password
            );
        }
        if ($strategy) {
            $this->setStrategy($strategy);
        }
        \Library\Base::registerHelper(
            'quote',
            function($data){
                return Adapter::getInstance()->quote($data);
            }
        );
        return $this;
    }

    public function setStrategy(Strategy $strategy)
    {
        $this->_connection = $strategy;
        $this->_connection->setAttribute(
            \PDO::ATTR_ERRMODE,
            \PDO::ERRMODE_EXCEPTION
        );
        return $this;
    }

    public function __call($name, $arguments)
    {
        if(is_null($this->_connection)){
            $this->connect();
        }
        $c = $this->_connection;
        return call_user_func_array(array($c, $name), $arguments);
    }

}