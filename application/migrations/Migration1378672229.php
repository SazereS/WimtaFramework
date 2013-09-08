<?php

class Application_Migrations_Migration1378672229 extends Library_Db_Migration{

    public $version = '1378672229';

    public function apply() {
        $this->createTable(
                'articles',
                array(
                    'title' => 'VARCHAR(100) NOT NULL',
                    'text' => 'TEXT NOT NULL',

                )
                );
    }

    public function rollback() {
        $this->dropTable('articles');
    }

}
