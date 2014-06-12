<?php

namespace Library\Db;

/**
 * Class Migration
 * @package Library\Db
 */
class Migration
{

    public $version = '';

    public function apply()
    {

    }

    public function rollback()
    {

    }

    public function __call($name, $arguments)
    {
        $c = Adapter::getInstance();
        return call_user_func_array(array($c, $name), $arguments);
    }

}