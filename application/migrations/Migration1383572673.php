<?php

namespace Application\Migrations;

class Migration1383572673 extends \Library\Db\Migration
{

    public $version = '1383572673';

    public function apply()
    {
        $this->createTable(
            'userlist',
            array(
                'login' => 'VARCHAR(20) NOT NULL',
                'password' => 'VARCHAR(32) NOT NULL',
                'salt' => 'VARCHAR(10) NOT NULL',
                'group' => 'ENUM(\'user\', \'admin\') NOT NULL DEFAULT \'user\''
            )
        );
    }

    public function rollback()
    {
        $this->dropTable('userlist');
    }

}
