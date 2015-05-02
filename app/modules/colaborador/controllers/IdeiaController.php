<?php

namespace Colaborador\Controllers;

use Phalcon\Http\Response;
use \Incentiv\Models\Ideia,
     Incentiv\Models\IdeiaPontuacao;

class IdeiaController extends ControllerBase {

    private $_auth;
    
    public function initialize() {
        $this->_auth = $this->auth->getIdentity();
        if (!$this->request->isAjax()) {

            $this->view->usuario_id        = $this->_auth['id'];
            $this->view->usuario_logado    = $this->auth->getName();
            $this->view->avatar            = $this->_auth['avatar'];
            $this->view->empresaId         = $this->_auth['empresaId'];
            $this->view->id                = $this->_auth['id'];
            $this->view->setTemplateBefore('private-colaborador');
        }
    }

    public function indexAction() {
        
    }

    public function ideiaAction() {
        $pontuacao_ideia = IdeiaPontuacao::build()->findFirst("empresaId = {$this->_auth['empresaId']} AND ativo = 'Y'");
        
        $this->view->pontuacaoIdeiaEnviada  = $pontuacao_ideia->pontuacaoIdeiaEnviada;
        $this->view->pontuacaoIdeiaAprovada = $pontuacao_ideia->pontuacaoIdeiaAprovada;
    }
    
    public function modalIdeiasAction(){
        $this->disableLayoutBefore();
        
        $objIdeia = new \stdClass();
        $objIdeia->usuarioId = $this->_auth['id'];
        
        $resultIdeiasUsuario  = Ideia::build()->buscarIdeiasUsuario($objIdeia);

        $this->view->ideias   = $resultIdeiasUsuario;
    } 
    
    public function salvarIdeiaAction(){
        $this->disableLayoutBefore();
  
        $objIdeia = new \stdClass();
        $objIdeia->id           = $this->request->getPost("id");
        $objIdeia->descricao    = $this->request->getPost("descricao");
        $objIdeia->titulo       = $this->request->getPost("titulo");
        $objIdeia->usuarioId    = $this->_auth['id'];
        $objIdeia->empresaId    = $this->_auth['empresaId'];

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