<?php

namespace Library\Module;

class Translator extends \Library\Module
{

    const STORAGE_ARRAY = 0;
    const STORAGE_TABLE = 1;
    const STORAGE_INI   = 2;

    private $_storage = self::STORAGE_ARRAY;
    private $_lang = '';
    private $_words = array();


    public function __construct()
    {
        parent::__construct();
        \Library\Base::registerHelper('_',
            function($key) {
                return $this->translate($key);
            }
        );
    }

    public function translate($key)
    {
        if(isset($this->_words[$key])){
            return $this->_words[$key];
        }
        return (string) $key;
    }

    public function setLanguage($lang)
    {
        $this->_lang = (string) $lang;
        return $this;
    }

    public function setStorage($storage)
    {
        if(in_array($storage, array(0,1,2))){
            $this->_storage = $storage;
        }
        return $this;
    }

    public function useIni($path, $prefix = '', $postfix = '')
    {
        $file = $path
            . '/'
            . (($prefix) ? $prefix . '.' : '')
            . $this->_lang
            . (($postfix) ? '.' . $postfix : '')
            . '.ini';
        if(!file_exists($file)){
            throw new Exception('Cannot find file "' . $file . '"');
        }
        $ini = parse_ini_file($file);
        $this->_words = $ini;
        return $this;
    }

}