<?php

namespace Instituicao\Controllers;

class IndexController extends ControllerBase
{
    public function initialize()
    {
        $this->view->setTemplateBefore('public');
    }
    
    public function indexAction(){}
}