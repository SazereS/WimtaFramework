<?php

namespace Library;

class Application
{

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

    public function __construct()
    {
        $this->_start_time = microtime(true);
        spl_autoload_register(function($path) {
                $path      = explode('\\', trim($path, '\\'));
                $class     = array_pop($path);
                $full_path = APPLICATION_PATH
                    . '..'
                    . DIRECTORY_SEPARATOR
                    . strtolower(
                        implode(DIRECTORY_SEPARATOR, $path)
                    )
                    . DIRECTORY_SEPARATOR
                    . $class
                    . '.php';
                if (!file_exists($full_path)) {
                    throw new Autoloader\Exception(
                        'Can\'t load class "'
                        . $class
                        . '": File not exists!'
                    );
                }
                require_once ($full_path);
            });
        set_exception_handler(function($exception){
                StackTrace::getInstance()->build()->show();
                Registry::getInstance()->log->show();
        });
        $this->_helpers = new Base();
    }

    /**
     *
     * @return \Library\Application
     */
    public function run()
    {
        $init            = new \Application\Init();
        $this->_request  = new Request();
        $this->_response = new Response();
        $this->_router   = new Router($this->_request);
        $log             = new Log();
        Registry::getInstance()
            ->set('request', $this->_request)
            ->set('response', $this->_response)
            ->set('router', $this->_router)
            ->set('log', $log);
        $log->write('Preinitialisation...');
        $init->preInit();
        $this->_router->findRoute();
        $log->write('Initialization...');
        $init->init();
        $view = new View(
            APPLICATION_PATH
            . 'views'
            . DIRECTORY_SEPARATOR
            . 'scripts'
            . DIRECTORY_SEPARATOR
            . $this->_request->getController()
        );
        $log->write('Calling ' . $this->_request->getController() . '->' . $this->_request->getAction() . '() ...');
        $this->loadController($this->_request->getController(), $view)
            ->initController()
            ->runControllerAction($this->_request->getAction());
        $log->write('Rendering...');
        if (!$this->_controller->view->rendered) {
            $this->_controller->view->render($this->_request->getAction());
        }
        $log->write('Responsing...');
        $this->_response
            ->renderLayout($this->_controller->view)
            ->writeContent();
        $log->write('Postinitialization...');
        $init->postInit();
        $log->writeSuccess('Application successfully executed for ' . $this->getElapsedTime() . ' seconds!');
        if(
            (Settings::getInstance()->debug_log_enable == true)
            AND ($this->_response->getFormat() == Response::FORMAT_HTML)
        ){
            $log->show();
        }
        return $this;
    }

    /**
     *
     * @param string $config
     * @param string $mode
     * @return \Library\Application
     */
    public function setConfig($config, $mode = 'production')
    {
        Settings::getInstance()
            ->setConfig((string) $config)
            ->loadConfig($mode);
        return $this;
    }

    /**
     *
     * @return float
     */
    public function getElapsedTime()
    {
        return microtime(true) - $this->_start_time;
    }

    /**
     *
     * @return Controller
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     *
     * @param string $controller_name
     * @param \Library\View $view
     * @return \Library\Application
     * @throws Application\Exception
     */
    public function loadController($controller_name, $view = NULL)
    {
        if ($view == NULL) {
            $view = new View();
        }
        $controller_name =
            '\\Application\\Controllers\\'
            . $this->_helpers->getRealName($controller_name)
            . 'Controller';
        try {
            $this->_controller       = new $controller_name;
            $this->_controller->view = $view;
        } catch (Autoloader\Exception $e) {
            if(Settings::getInstance()->error_page404){
                \Helpers\Page404::page404();
            }
            throw new Application\Exception(
                'Cannot find controller class "'
                . $controller_name
                . '"'
            );
        }
        return $this;
    }

    /**
     *
     * @param string $action_name
     * @return mixed
     * @throws Controller\Exception
     */
    public function runControllerAction($action_name)
    {
        $action_name = $this->_helpers->getRealName($action_name) . 'Action';
        if (method_exists($this->_controller, $action_name)) {
            return $this->_controller->$action_name();
        } else {
            if (Settings::getInstance()->error_page404) {
                \Helpers\Page404::page404();
            }
            throw new Controller\Exception(
                'Controller "'
                . $this->_request->getController()
                . '" hasn\'t got method "'
                . $action_name
                . '"'
            );
        }
    }

    /**
     *
     * @return \Library\Application
     */
    public function initController()
    {
        if (method_exists($this->_controller, 'init')) {
            $this->_controller->init();
        }
        return $this;
    }

}