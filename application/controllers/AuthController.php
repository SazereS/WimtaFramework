<?php

namespace Application\Controllers;

use \Application\Models;

class AuthController extends \Library\Controller
{

    public function init()
    {
        // Initialization code here
    }

    public function indexAction()
    {
        $this->getModule('auth')->setData('admin', 'passwd')->signIn();
        $this->redirect();
    }

    public function logoutAction()
    {
        $this->getModule('auth')->signOut();
        $this->redirect();
    }

}