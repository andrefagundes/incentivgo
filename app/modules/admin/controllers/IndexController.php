<?php

namespace Incentiv\Controllers;

use Incentiv\Forms\ContatoForm;

class IndexController extends ControllerBase
{
    public function initialize()
    {
        $this->view->setTemplateBefore('public');
    }
    
    public function indexAction(){
        
        $form = new ContatoForm();
        
        $this->view->form = $form;
    }
    public function componentesAction(){
        $this->view->setTemplateBefore('public_session');
    }
    
    public function contatoAction()
    {
        $this->view->setTemplateBefore('public_session');
         
        $form = new ContatoForm();

        if ($this->request->isPost()) {

            if ($form->isValid($this->request->getPost()) != false) {
                var_dump($this->request->getPost());die;
            } 
        }

        $this->view->form = $form;
    }
}