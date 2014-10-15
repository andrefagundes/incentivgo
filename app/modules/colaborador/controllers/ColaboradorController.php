<?php

namespace Colaborador\Controllers;

class ColaboradorController extends ControllerBase
{
    public function initialize()
    {
        $this->view->setTemplateBefore('private-colaborador');
    }
    
    public function indexAction(){}
}