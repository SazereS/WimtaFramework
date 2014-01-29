<?php

namespace Library;

class Module
{

    static private $_modules = array();

    public function __construct()
    {
        if(get_called_class() != 'Library\\Module'){
            $module = explode('\\', get_called_class());
            self::$_modules[strtolower(end($module))] = $this;
            Registry::getInstance()->log->write('> Module ' . end($module) . ' loaded!');
        } else {
            Registry::getInstance()->modules = $this;
            Base::registerHelper(
                'getModule',
                function($name){
                    return Registry::getInstance()->modules->{(string) $name};
                }
            );
        }
    }

    public function __get($name)
    {
        return self::$_modules[strtolower($name)];
    }

    final public function load($module)
    {
        $name = '\\Library\\Module\\' . (string) $module;
        try {
            new $name();
        } catch (\Exception $e){
            throw new Module\Exception($e->getMessage());
        }
    }

    final public function autoload()
    {
        $modules = Settings::getInstance()->modules_autoload;
        if(is_array($modules)){
            foreach ($modules as $module){
                $this->load($module);
            }
        }
        return $this;
    }

}