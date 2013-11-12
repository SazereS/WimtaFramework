<?php

namespace Application\Migrations;

class Migration1384180302 extends \Library\Db\Migration
{

    public $version = '1384180302';

    public function apply()
    {
        $this->updateTable(
            'userlist',
            array(
                'add' => array(
                    'age' => 'INT(3) NOT NULL DEFAULT 0'
                )
            )
        );
    }

    public function rollback()
    {
        $this->updateTable(
            'userlist',
            array(
                'drop' => array(
                    'age'
                )
            )
        );
    }

}
