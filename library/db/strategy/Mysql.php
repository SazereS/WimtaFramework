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

}