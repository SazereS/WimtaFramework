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
        $this->_helpers = new Base();
    }

    /**
     * @todo Удалить этот метод
     */
    public function initDbAdapter()
    {
        return $this;
    }

    public function run()
    {
        $init            = new \Application\Init();
        $this->_request  = new Request();
        $this->_response = new Response();
        Registry::getInstance()
            ->set('request', $this->_request)
            ->set('response', $this->_response);
        $init->preInit();
        $this->_router   = new Router($this->_request);
        $this->_router->findRoute();
        $init->init();
        $view            = new View(
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
        if (!$this->_controller->view->rendered) {
            $this->_controller->view->render($this->_request->getAction());
        }
        $this->_response
            ->renderLayout($this->_controller->view)
            ->writeContent();
        $init->postInit();
        return $this;
    }

    public function setConfig($config, $mode = 'production')
    {
        Settings::getInstance()
            ->setConfig((string) $config)
            ->loadConfig($mode);
        return $this;
    }

    public function getElapsedTime()
    {
        return microtime(true) - $this->_start_time;
    }

    public function getController()
    {
        return $this->_controller;
    }

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
            throw new Application\Exception(
                'Cannot find controller class "'
                . $controller_name
                . '"'
            );
        }
        return $this;
    }

    public function runControllerAction($action_name)
    {
        $action_name = $this->_helpers->getRealName($action_name) . 'Action';
        if (method_exists($this->_controller, $action_name)) {
            return $this->_controller->$action_name();
        } else {
            throw new Controller\Exception(
                'Controller "'
                . $this->_request->getController()
                . '" hasn\'t got method "'
                . $action_name
                . '"'
            );
        }
    }

    public function initController()
    {
        if (method_exists($this->_controller, 'init')) {
            $this->_controller->init();
        }
        return $this;
    }

}