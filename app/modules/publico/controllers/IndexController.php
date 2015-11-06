<?php

namespace Publico\Controllers;

use Publico\Forms\ContatoForm,
    \Incentiv\Models\Lang;

class IndexController extends ControllerBase
{
    private $_lang = array();
    
    public function initialize()
    {
//        $senha = '';
//        $senhacript = $this->security->hash($senha);
//        echo $senhacript;die;
        $funcoes = $this->getDI()->getShared('funcoes');
        $subdominio = $funcoes->before('.incentivgo', $this->config->application->publicUrl);

        if($subdominio != "" && $subdominio != 'www' && $subdominio != 'beta')
        {
            $dominio = $funcoes->after('.', $this->config->application->publicUrl); 
            $this->response->redirect("http://www.{$dominio}/corporation", true);
        }
        
        $this->_lang = parent::initialize();
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
                    'nome_usuario'  => $this->request->getPost('nome', 'striptags'),
                    'email'         => $this->request->getPost('email', 'email'),
                    'mensagem'      => $this->request->getPost('description', 'striptags'), 
                    'nome'          => $this->_lang['nome'], 
                    'contato'       => $this->_lang['contato'] 
                ));

                $this->flash->success($this->_lang['mensagem_enviada']);
                $form->clear();
            } 
        }

        $this->view->form = $form;
    }
    
    public function langAction(){
//        $this->session->remove("lang");
        $lang = $this->dispatcher->getParam("lang");
        
        if($lang == 'pt-br' || $lang == 'en' ){
            $this->session->set('lang', $lang);
            $ipaddress = $this->request->getClientAddress();
            Lang::build()->inserirAlterarLang($ipaddress,$lang);
        }

        $this->response->redirect('');
    }

    public function route404Action(){
        echo 'Ops, deu errado';
    }
}