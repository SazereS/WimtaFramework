<?php

class Library_Application{

    // VARIABLES

    /**
     *
     * @var float
     */
    private $_start_time;

    /**
     *
     * @var Library_Request
     */
    private $_request;

    /**
     *
     * @var Library_Response
     */
    private $_response;

    /**
     *
     * @var Library_Router
     */
    private $_router;

    /**
     *
     * @var Library_Controller
     */
    private $_controller;

    // PRIVATE METHODS

    // PUBLIC METHODS
    public function __construct() {
        $this->_start_time = microtime(true);
        spl_autoload_register(function($class){
            $path = explode('_', strtolower($class));
            $file = array_pop($path);
            $file[0] = strtoupper($file[0]);
            $path = implode(DIRECTORY_SEPARATOR, $path);
            $path = APPLICATION_PATH
                    . '..'
                    . DIRECTORY_SEPARATOR
                    . $path
                    . DIRECTORY_SEPARATOR
                    . $file
                    . '.php';
            if(!file_exists($path)){
                throw new Library_Autoloader_Exception('Can\'t load class "' . $class . '": File not exists!');
            }
                require_once ($path);
        });
        $this->_helpers = new Library_Base();
    }

    public function initDbAdapter() {
<<<<<<< HEAD
        $strategy = new Library_Db_Strategy_Mysql(
                'localhost',
                'test',
                'root',
                ''
                );
        Library_Db_Adapter::getInstance()->setStrategy($strategy);
=======
        $strategy = false;
        if(Library_Settings::getInstance()->db_driver == 'mysql'){
            $strategy = new Library_Db_Strategy_Mysql(
                    Library_Settings::getInstance()->db_mysql_host,
                    Library_Settings::getInstance()->db_mysql_dbname,
                    Library_Settings::getInstance()->db_mysql_login,
                    Library_Settings::getInstance()->db_mysql_password
            );
        }
        if($strategy){
            Library_Db_Adapter::getInstance()->setStrategy($strategy);
        }
        return $this;
>>>>>>> Still working on DB and manager functionality
    }

    public function run(){
        $this->_request  = new Library_Request();
        $this->_response = new Library_Response();
        Library_Registry::getInstance()
                ->set('request', $this->_request)
                ->set('response', $this->_response);
        $this->_router   = new Library_Router($this->_request);
        $this->_router->findRoute();
        $this->initDbAdapter();
        $init = new Application_Init();
        $init->init();
        $view = new Library_View(
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
        Library_Settings::getInstance()->setConfig((string) $config)->loadConfig($mode);
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
            $view = new Library_View();
        }
        $controller_name =
                'Application_Controllers_'
                . $this->_helpers->getRealName($controller_name)
                . 'Controller';
        try{
            $this->_controller = new $controller_name;
            $this->_controller->view = $view;
        } catch(Library_Autoloader_Exception $e){
            throw new Library_Application_Exception('Cannot find controller class "' . $controller_name . '"');
        }
        return $this;
    }

    public function runControllerAction($action_name){
        $action_name = $this->_helpers->getRealName($action_name) . 'Action';
        if(method_exists($this->_controller, $action_name)){
            return $this->_controller->$action_name();
        } else {
            throw new Library_Controller_Exception('Controller "' . $this->_request->getController() . '" hasn\'t got method "' . $action_name . '"');
        }
    }

    public function initController(){
        if(method_exists($this->_controller, 'init')){
            $this->_controller->init();
        }
        return $this;
    }

}