<?php

namespace Application;

class Init extends \Library\Init
{

    public function _initTest()
    {

    }

    public function _initAuth()
    {
        $auth = $this->getModule('auth');
        $auth->setTable('userlist')
            ->setIdCol('login')
            ->setPasswordCol('password')
            ->setSaltCol('salt');
        $auth->signIn();
    }

    public function _initAcl()
    {
        $acl = $this->getModule('acl');

        $acl->addGroup('guest');
        $acl->deny('guest');
        $acl->allow('guest', 'index');
        $acl->allow('guest', 'errors');

        $acl->addGroup('user');
        $acl->allow('user');
        $acl->deny('user', 'admin');

        $acl->addGroup('admin');
        $acl->allow('admin');

        $user = $this->getModule('auth')->getUser();
        $acl->setGroup(($user) ? $user['group'] : 'guest');
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