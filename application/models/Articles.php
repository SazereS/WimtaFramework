<?php

class Application_Models_Articles extends Library_Db_Table{

    public function __construct() {
        $this->_table = 'articles';
    }

}
