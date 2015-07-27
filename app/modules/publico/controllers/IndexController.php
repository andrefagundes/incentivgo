<?php

namespace Publico\Controllers;

use Publico\Forms\ContatoForm;

class IndexController extends ControllerBase
{
    public function initialize()
    {
        $funcoes = $this->getDI()->getShared('funcoes');
        $subdominio = $funcoes->before('.incentivgo', $this->config->application->publicUrl);

        if($subdominio != "" && $subdominio != 'www')
        {
            $dominio = $funcoes->after('.', $this->config->application->publicUrl); 
            $this->response->redirect("http://www.{$dominio}/corporation", true);
        }
        
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
            //valida formulário de contato e envia email para Incentiv Go
            if ($form->isValid($this->request->getPost()) != false) {
                $this->getDI()
                    ->getMail()
                    ->send(array(
                    'Incentiv GO' => 'amfcom@gmail.com'
                ), "Contatos", 'contato', array(
                    'nome'      => $this->request->getPost('nome', 'striptags'),
                    'email'     => $this->request->getPost('email', 'email'),
                    'mensagem'  => $this->request->getPost('description', 'striptags')  
                ));

                $this->flash->success('Mensagem enviada com sucesso, aguarde contato');
                $form->clear();
            } 
        }

        $this->view->form = $form;
    }
    
    public function route404Action(){
        echo 'Ops, deu errado';
    }
}