<?php

namespace Application\Migrations;

class Migration1378991478 extends \Library\Db\Migration
{

    public $version = '1378991478';

    public function apply()
    {
        $this->createTable(
            'comments',
            array(
                'article_id' => 'INTEGER(11) NOT NULL',
                'author'     => 'VARCHAR(32) NOT NULL',
                'text'       => 'TEXT NOT NULL'
            )
        );
    }

    public function rollback()
    {
        $this->dropTable('comments');
    }

}
