<?php

namespace Library\Db\Strategy;

class Mysql extends Prototype
{

    public function __construct(
        $host,
        $dbname,
        $username,
        $passwd,
        $options = NULL
    )
    {
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
    )
    {
        $additional_params = array_merge(
            array(
                'collate' => 'utf8_general_ci',
                'engine'  => 'InnoDB'
            ),
            $additional_params
        );
        $default_fields = array(
            'id'         => 'INT(10) NOT NULL AUTO_INCREMENT',
            'created_at' => 'DATETIME NOT NULL',
            'updated_at' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        );
        $q = 'CREATE TABLE `' . $table_name
            . '` (';
        if ($use_default_fields) {
            $fields = array_merge($default_fields, $fields);
        }
        $q_fields = array();
        foreach ($fields as $key => $val) {
            $q_fields[] = '`' . $key . '` ' . $val;
        }
        $q .= implode(', ', $q_fields);
        $q .= ', PRIMARY KEY(`'
            . $primary
            . '`) ) COLLATE=\''
            . $additional_params['collate']
            . '\' ENGINE='
            . $additional_params['engine'];
        try {
            \Library\Db\Adapter::getInstance()->exec($q);
        } catch (PDOException $e) {
            throw new \Library\Db\Exception(
                'MIGRATION EXCEPTION! ' . $e->getMessage()
            );
        }
    }

    /**
     * @todo Добавить функционал изменения структуры таблицы
     */
    public function updateTable($table_name, array $fields)
    {
        $query = 'ALTER TABLE `' . (string) $table_name . '` ';
        $add    = @$fields['add'];
        $drop   = @$fields['drop'];
        $change = @$fields['change'];
        $columns = array();
        if($add){
            foreach($add as $col => $params){
                $columns[] = 'ADD COLUMN `' . $col . '` ' . $params;
            }
        }
        if($drop){
            foreach($drop as $col){
                $columns[] = 'DROP COLUMN `' . $col . '`';
            }
        }
        if($change){
            foreach($change as $col => $params){
                if(!is_array($params)){
                    $columns[] = 'ADD COLUMN `' . $col . '` `' . $col . '` ' . $params;
                } else {
                    $new_name = reset($params);
                    $params = next($params);
                    $columns[] = 'ADD COLUMN `' . $col . '` `' . $new_name . '` ' . $params;
                }
            }
        }
        $query .= implode(', ', $columns);
        $query .= ';';
        try{
            \Library\Db\Adapter::getInstance()->exec($query);
        } catch (PDOException $e) {
            throw new \Library\Db\Exception('MIGRATION EXCEPTION! ' . $e->getMessage());
        }
    }

    public function dropTable($table_name)
    {
        try {
            \Library\Db\Adapter::getInstance()->exec('DROP TABLE `' . $table_name . '`');
        } catch (PDOException $e) {
            throw new \Library\Db\Exception('MIGRATION EXCEPTION! ' . $e->getMessage());
        }
    }

}