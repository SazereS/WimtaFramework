<?php

namespace Library;

/**
 *
 * @method \Library\Registry getRegistry() Fast registry access
 * @method \Library\Module\$module_name getModule(\string $module_name) Returns selected module, if it's already loaded
 * @method string baseUrl(\string $url = '') Returns link from root of site
 * @method boolean isPost() Checking existence of some data sended using POST method
 * @method array getPost() Returns $_POST
 * @method string getRealName(\string $name)
 * @method array objectsToArray() Recursively converts objects to array
 * @method void page404() Redirects to the 404 page, if isset config parameter "error_page404", else redirects to root of the site
 * @method void redirect(\string $to = '') Redirects to the new location
 * @method Log writeLog(\mixed $message = null)
 * @method \string xmlEncode(array $array, \string $root = 'xmldata', \integer $level = 0) Converts array to XML data
 * @method Request getRequest() Returns Request object
 * @method \Library\Response getResponse() Returns Response object
 * @method \string getParam(\string $key) Returns param from URL
 *
 */
class Base
{

    static private $_helpers;

    public function __call($name, $arguments)
    {
        if(isset(self::$_helpers[strtolower($name)])){
            return call_user_func_array(self::$_helpers[strtolower($name)], $arguments);
        }
        $name[0] = strtoupper($name[0]);
        $class_name = '\\Helpers\\' . $name;
        return call_user_func_array($class_name . '::' . $name, $arguments);
    }

    public static function __callStatic($name, $arguments)
    {
        if (isset(self::$_helpers[strtolower($name)])) {
            return call_user_func_array(self::$_helpers[strtolower($name)],
                                                                   $arguments);
        }
        $name[0] = strtoupper($name[0]);
        $class_name = '\\Helpers\\' . $name;
        return call_user_func_array($class_name . '::' . $name, $arguments);
    }

    static public function registerHelper($helper, $callback){
        if(is_callable($callback)){
            self::$_helpers[strtolower($helper)] = $callback;
            return true;
        } else {
            \Library\Registry::getInstance()->log->writeError('Cannot register helper "' . $helper . '": not callable!');
            return false;
        }
    }

}