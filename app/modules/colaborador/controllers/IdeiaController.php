<?php

namespace Colaborador\Controllers;

use Phalcon\Http\Response;
use \Incentiv\Models\Ideia;

class IdeiaController extends ControllerBase {

    public function initialize() {
        if (!$this->request->isAjax()) {
            $auth = $this->auth->getIdentity();
            $this->view->usuario_id        = $auth['id'];
            $this->view->usuario_logado    = $this->auth->getName();
            $this->view->avatar            = $auth['avatar'];
            $this->view->empresaId         = $auth['empresaId'];
            $this->view->id                = $auth['id'];
            $this->view->setTemplateBefore('private-colaborador');
        }
    }

    public function indexAction() {
        
    }

    public function ideiaAction() {
    }
    
    public function modalIdeiasAction(){
        $this->disableLayoutBefore();
        
        $auth = $this->auth->getIdentity();
        
        $objIdeia = new \stdClass();
        $objIdeia->usuarioId = $auth['id'];
        
        $resultIdeiasUsuario  = Ideia::build()->buscarIdeiasUsuario($objIdeia);

        $this->view->ideias   = $resultIdeiasUsuario;
    } 
    
    public function salvarIdeiaAction(){
        $this->disableLayoutBefore();
        
        $auth = $this->auth->getIdentity();
  
        $objIdeia = new \stdClass();
        $objIdeia->id           = $this->request->getPost("id");
        $objIdeia->descricao    = $this->request->getPost("descricao");
        $objIdeia->titulo       = $this->request->getPost("titulo");
        $objIdeia->usuarioId    = $auth['id'];
        $objIdeia->empresaId    = $auth['empresaId'];

        $resultIdeia = Ideia::build()->salvarIdeia($objIdeia);

        if($resultIdeia['status'] == 'ok')
        {
            $this->flashSession->success($resultIdeia['message']);
        }else{
            $this->flashSession->error($resultIdeia['message']);
        }

        $this->response = new Response();
        $this->response->setJsonContent($resultIdeia['status'],'utf8');
        $this->response->send();

    }

}