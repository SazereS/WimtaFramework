<?php

class Library_Db_Strategy_Mysql extends Library_Db_Strategy_Prototype{

    public function __construct($host, $dbname, $username, $passwd, $options = NULL) {
        parent::__construct(
                'mysql:dbname=' . $dbname . ';host=' . $host,
                $username,
                $passwd,
                $options
                );
    }

    public function createTable(
            $table_name,
            array $fields,
            $use_default_fields = true,
            $primary = 'id',
            array $additional_params = array()
            ){
        $additional_params = array_merge(
                array(
                    'collate' => 'utf8_general_ci',
                    'engine' => 'InnoDB'
                ),
                $additional_params
                );
        $default_fields = array(
            'id' => 'INT(10) NOT NULL AUTO_INCREMENT',
            'created_at' => 'DATETIME NOT NULL',
            'updated_at' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        );
        $q = 'CREATE TABLE `' . $table_name
                . '` (';
        if($use_default_fields){
            $fields = array_merge($default_fields, $fields);
        }
        $q_fields = array();
        foreach($fields as $key => $val){
            $q_fields[] = '`' . $key . '` ' . $val;
        }
        $q .= implode(', ', $q_fields);
        $q .= ', PRIMARY KEY(`' . $primary . '`) ) COLLATE=\'' . $additional_params['collate'] . '\' ENGINE=' . $additional_params['engine'];
        try{
            Library_Db_Adapter::getInstance()->exec($q);
        } catch(PDOException $e){
            throw new Library_Db_Exception('MIGRATION EXCEPTION! ' . $e->getMessage());
        }
    }

    public function updateTable() {
        # Need to add
    }

    public function dropTable($table_name) {
        try{
            Library_Db_Adapter::getInstance()->exec('DROP TABLE `' . $table_name . '`');
        } catch(PDOException $e){
            throw new Library_Db_Exception('MIGRATION EXCEPTION! ' . $e->getMessage());
        }
    }

}