<?php

class Library_Db_Table{

    private $_table;
    private $_adapter;

    /**
     *
     * @var PDOStatement
     */
    public $temp;

    public function __construct(Library_Db_Adapter $adapter, $table) {
        $this->_adapter = $adapter;
        $this->_table = $table;
    }

    public function fetchAll(){
        $this->temp = $this->_adapter->getInstance()->fetchAll($this->_table);
        while($data = $this->temp->fetch(PDO::FETCH_ASSOC)){
            $rows[] = new Library_Db_Table_Row($data);
        }
        return $rows;
    }

}