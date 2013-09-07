<?php

class Application_Init extends Library_Init{

    public function _initTest(){
        $strategy = new Library_Db_Strategy_Mysql(
                'localhost',
                'test',
                'root',
                ''
                );
        $adapter = Library_Db_Adapter::getInstance()->setStrategy($strategy);
    }

}