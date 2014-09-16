<?php

namespace Admin\Controllers;

class AdminController extends ControllerBase
{
    public function initialize()
    {
        $this->view->setTemplateBefore('public');
    }
    
    public function indexAction(){}
}