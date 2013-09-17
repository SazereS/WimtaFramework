<?php

namespace Application\Controllers;

use \Application\Models;

class ErrorsController extends \Library\Controller
{

    public function init()
    {
        // Initialization code here
    }

    public function indexAction()
    {
        $this->page404();
    }

    public function page404Action()
    {
        // Put your code here
    }

}