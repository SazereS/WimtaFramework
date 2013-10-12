<?php

namespace Application\Migrations;

class Migration1378672229 extends \Library\Db\Migration
{

    public $version = '1378672229';

    public function apply()
    {
        $this->createTable(
            'articles',
            array(
                'title' => 'VARCHAR(100) NOT NULL',
                'text'  => 'TEXT NOT NULL',
            )
        );
    }

    public function rollback()
    {
        $this->dropTable('articles');
    }

}
