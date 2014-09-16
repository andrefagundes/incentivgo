<?php

namespace Publico\Controllers;

use Publico\Forms\ContatoForm;

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
    
    public function contatoAction()
    {
        $this->view->setTemplateBefore('public_session');
         
        $form = new ContatoForm();

        if ($this->request->isPost()) {
            //valida formulário de contato e envia email para Incentiv Educ
            if ($form->isValid($this->request->getPost()) != false) {
                $this->getDI()
                    ->getMail()
                    ->send(array(
                    'amfcom@gmail.com' => 'Incentiv Educ'
                ), "Contatos", 'contato', array(
                    'nome'      => $this->request->getPost('nome', 'striptags'),
                    'email'     => $this->request->getPost('email', 'email'),
                    'mensagem'  => $this->request->getPost('description', 'striptags')  
                ));
                
                $this->flash->success('Mensagem enviada com sucesso, aguarde contato');
            } 
        }

        $this->view->form = $form;
    }
    
    public function route404Action(){
        echo 'Ops, deu errado';
    }
}