<?php

namespace Application;

class Init extends \Library\Init
{

    public function _initTest()
    {
        \Library\Db\Adapter::getInstance();
    }

    public function preInit()
    {
        $router = \Library\Registry::getInstance()->router;
        $router->addRoute(
            'quick-start',
            array(
                'controller' => 'guides',
                'action'     => 'quick-start'
            )
        );
        $router->addRoute(
            'structure',
            array(
            'controller' => 'guides',
            'action'     => 'structure'
            )
        );
        $router->addRoute(
            'api',
            array(
                'controller' => 'guides',
                'action'     => 'api'
            )
        );
    }

}