<?php

namespace Application;

class Init extends \Library\Init
{

    public function _initAcl()
    {
        $acl = new \Library\Module\Acl();

        $acl->addGroup('guest');
        $acl->deny('guest');
        $acl->allow('guest', 'index');
        $acl->allow('guest', 'errors');

        $acl->addGroup('user');
        $acl->allow('user');
        $acl->deny('user', 'admin');

        $acl->setGroup('user');
        if(!$acl->isAllowed()){
            $this->redirect('errors/page403');
        }
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