<?php

class Library_Settings extends Library_Registry{

    private $_config;

    public function setConfig($config){
        $this->_config = $config;
        return $this;
    }

    public function loadConfig(){
        $this->_data = parse_ini_file(
                APPLICATION_PATH
                . DIRECTORY_SEPARATOR
                . 'config'
                . DIRECTORY_SEPARATOR
                . $this->_config
                . '.ini',
                true
                );
        return $this;
    }

}