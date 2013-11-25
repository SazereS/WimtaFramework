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

        $acl->addGroup('guest')
            ->deny()
            ->allow('index')
            ->allow('errors')
            ->allow('auth');

        $acl->addGroup('user')
            ->allow()
            ->deny('admin');

        $acl->addGroup('admin')
            ->allow();

        $user = $this->getAuth('group');
        $acl->setGroup(($user) ? $user : 'guest');
        if(!$acl->isAllowed()){
            $this->redirect('errors/page403');
        }
    }

    public function _initTest2()
    {

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