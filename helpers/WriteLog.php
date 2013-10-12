<?php

namespace Helpers;

class WriteLog
{

    public static function writeLog($message = null)
    {
        if(is_null($message)){
            return \Library\Registry::getInstance()->log;
        } else {
            \Library\Registry::getInstance()->log->write((string) $message);
        }
    }

}