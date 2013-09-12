<?php

namespace Library;

class Settings extends Registry
{

    private $_config;

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
        } else {
            $this->_data = array_merge($data['development'], $data['production']);
        }
        return $this;
    }

}