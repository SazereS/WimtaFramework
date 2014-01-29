<?php

namespace Library;

class Settings extends Registry
{

    private $_config;
    private $_mode;

    public function setConfig($config)
    {
        $this->_config = $config;
        return $this;
    }

    public function loadConfig($mode = 'production')
    {
        $data = parse_ini_file(
            APPLICATION_PATH
            . DIRECTORY_SEPARATOR
            . 'config'
            . DIRECTORY_SEPARATOR
            . $this->_config
            . '.ini',
            true
        );
        if ($mode == 'development') {
            $this->_data = $data['development'];
            $this->_mode = 'development';
        } else {
            $this->_data = array_merge($data['development'], $data['production']);
            $this->_mode = 'production';
        }
        return $this;
    }

    public function getMode()
    {
        return $this->_mode;
    }

}