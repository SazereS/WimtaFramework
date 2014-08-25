<?php

namespace Library\Module;

class Translator extends \Library\Module
{

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

    public function useTable($table, $key_column = 'key')
    {
        $rows = \Library\Db\Adapter::getInstance()->fetchAll($table);
        $array = array();
        foreach($rows->fetchAll(\PDO::FETCH_ASSOC) as $rows){
            $array[$rows[$key_column]] = $rows[$this->_lang];
        }
        $this->_words = $array;
        return $this;
    }

}