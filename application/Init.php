<?php

namespace Application;

class Init extends \Library\Init{

    public function _initTest(){
        \Library\Db\Adapter::getInstance();
    }

}