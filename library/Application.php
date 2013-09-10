<?php

namespace Library;

class Application{

    // VARIABLES

    /**
     *
     * @var float
     */
    private $_start_time;

    /**
     *
     * @var \Library\Request
     */
    private $_request;

    /**
     *
     * @var \Library\Response
     */
    private $_response;

    /**
     *
     * @var \Library\Router
     */
    private $_router;

    /**
     *
     * @var \Library\Controller
     */
    private $_controller;

    // PRIVATE METHODS

    // PUBLIC METHODS
    public function __construct() {
        $this->_start_time = microtime(true);
        spl_autoload_register(function($path){
            $path      = explode('\\', trim($path, '\\'));
            $class     = array_pop($path);
            $full_path = APPLICATION_PATH
                    . '..'
                    . DIRECTORY_SEPARATOR
                    . strtolower(implode(DIRECTORY_SEPARATOR, $path))
                    . DIRECTORY_SEPARATOR
                    . $class
                    . '.php';
            if(!file_exists($full_path)){
                throw new \Library\Autoloader\Exception('Can\'t load class "' . $class . '": File not exists!');
            }
            require_once ($full_path);
        });
        $this->_helpers = new \Library\Base();
    }

    public function initDbAdapter() {
        $strategy = new \Library\Db\Strategy\Mysql(
                'localhost',
                'test',
                'root',
                ''
                );
        \Library\Db\Adapter::getInstance()->setStrategy($strategy);
        $strategy = false;
        if(\Library\Settings::getInstance()->db_driver == 'mysql'){
            $strategy = new \Library\Db\Strategy\Mysql(
                    \Library\Settings::getInstance()->db_mysql_host,
                    \Library\Settings::getInstance()->db_mysql_dbname,
                    \Library\Settings::getInstance()->db_mysql_login,
                    \Library\Settings::getInstance()->db_mysql_password
            );
        }
        if($strategy){
            \Library\Db\Adapter::getInstance()->setStrategy($strategy);
        }
        return $this;
    }

    public function run(){
        $this->_request  = new \Library\Request();
        $this->_response = new \Library\Response();
        \Library\Registry::getInstance()
                ->set('request', $this->_request)
                ->set('response', $this->_response);
        $this->_router   = new \Library\Router($this->_request);
        $this->_router->findRoute();
        $this->initDbAdapter();
        $init = new \Application\Init();
        $init->init();
        $view = new \Library\View(
                APPLICATION_PATH
                . 'views'
                . DIRECTORY_SEPARATOR
                . 'scripts'
                . DIRECTORY_SEPARATOR
                . $this->_request->getController()
                );
        $this->loadController($this->_request->getController(), $view)
                ->initController()
                ->runControllerAction($this->_request->getAction());
        if(!$this->_controller->view->rendered){
            $this->_controller->view->render($this->_request->getAction());
        }
        $this->_response->renderLayout($this->_controller->view)->writeContent();
        return $this;
    }

    public function setConfig($config, $mode = 'production'){
        \Library\Settings::getInstance()->setConfig((string) $config)->loadConfig($mode);
        return $this;
    }

    public function getElapsedTime(){
        return microtime(true) - $this->_start_time;
    }

    public function getController(){
        return $this->_controller;
    }

    public function loadController($controller_name, $view = NULL){
        if($view == NULL){
            $view = new \Library\View();
        }
        $controller_name =
                '\\Application\\Controllers\\'
                . $this->_helpers->getRealName($controller_name)
                . 'Controller';
        try{
            $this->_controller = new $controller_name;
            $this->_controller->view = $view;
        } catch(\Library\Autoloader\Exception $e){
            throw new \Library\Application\Exception('Cannot find controller class "' . $controller_name . '"');
        }
        return $this;
    }

    public function runControllerAction($action_name){
        $action_name = $this->_helpers->getRealName($action_name) . 'Action';
        if(method_exists($this->_controller, $action_name)){
            return $this->_controller->$action_name();
        } else {
            throw new \Library\Controller\Exception('Controller "' . $this->_request->getController() . '" hasn\'t got method "' . $action_name . '"');
        }
    }

    public function initController(){
        if(method_exists($this->_controller, 'init')){
            $this->_controller->init();
        }
        return $this;
    }

}