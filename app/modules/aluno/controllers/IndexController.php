<?php

namespace Aluno\Controllers;

class IndexController extends ControllerBase
{
    public function initialize()
    {
        $this->view->setTemplateBefore('private');
    }
    
    public function indexAction(){}
}