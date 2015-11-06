<?php

namespace Colaborador\Controllers;

use Phalcon\Http\Response;
use Incentiv\Models\DesafioUsuario,
    Incentiv\Models\Ajuda,
    Incentiv\Models\Anotacao,
    Incentiv\Models\UsuarioPontuacaoCredito,
    Incentiv\Models\UsuarioPontuacaoDebito,
    Incentiv\Models\Noticia;

class ColaboradorController extends ControllerBase
{
    private $_auth;
    private $_lang;
     
    public function initialize()
    {
        $this->_auth = $this->auth->getIdentity();

        $this->view->count_desafios    = DesafioUsuario::build()->count("usuarioId = ".$this->_auth['id']." AND ( usuarioResposta IS NULL OR usuarioResposta != 'N' ) AND envioAprovacaoDt IS NULL");
        $this->view->count_ajudas      = Ajuda::build()->count("ativo = 'Y' AND ajudaId IS NULL");
        $this->view->count_anotacoes   = Anotacao::build()->count("usuarioId = ".$this->_auth['id']);
        $this->view->count_noticias    = Noticia::build()->count("empresaId = ".$this->_auth['empresaId']);
        $this->view->count_pontuacao        = UsuarioPontuacaoCredito::build()->buscarPontuacaoUsuario($this->_auth['id']);
        $this->view->count_pontuacao_ganha  = (int) UsuarioPontuacaoCredito::build()->sum(array("column" => "pontuacao", "conditions" => "usuarioId = {$this->_auth['id']}"));
        $this->view->count_pontuacao_usada  = (int) UsuarioPontuacaoDebito::build()->sum(array("column" => "pontuacao", "conditions" => "usuarioId = {$this->_auth['id']}"));
        $this->view->usuario_logado    = $this->auth->getName();
        $this->view->avatar            = $this->_auth['avatar'];
        $this->view->empresaId         = $this->_auth['empresaId'];
        $this->view->id                = $this->_auth['id'];
        $this->view->ano               = date('Y');
        if (!$this->request->isAjax()) {
            $this->view->setTemplateBefore('private-colaborador');
        }
        $this->_lang = parent::initialize();
    }
    
    public function indexAction(){}
 
    public function modalAnotacoesAction(){
        $this->disableLayoutBefore();

        $resultAnotacoes  = Anotacao::build()->buscarAnotacoes($this->_auth['id']);

        $this->view->anotacoes   = $resultAnotacoes; 
    }
    
    public function salvarAnotacaoAction(){
        $this->disableLayoutBefore();
        
        $objAnotacao = new \stdClass();
        $objAnotacao->descricao    = $this->request->getPost("descricao");
        $objAnotacao->usuarioId    = $this->_auth['id'];

        $resultAnotacao = Anotacao::build()->salvarAnotacao($objAnotacao);

        if($resultAnotacao['status'] == 'ok')
        {
            $this->flashSession->success($resultAnotacao['message']);
        }else{
            $this->flashSession->error($resultAnotacao['message']);
        }

        $this->response = new Response();
        $this->response->setJsonContent($resultAnotacao['status'],'utf8');
        $this->response->send();

    }
    
    public function excluirAnotacaoAction(){
        $this->disableLayoutBefore();
        
        $objAnotacao                = new \stdClass();
        $objAnotacao->anotacaoId    = $this->request->getPost("anotacaoId");

        $resultAnotacao = Anotacao::build()->excluirAnotacao($objAnotacao);

        if($resultAnotacao['status'] == 'ok')
        {
            $this->flashSession->success($resultAnotacao['message']);
        }else{
            $this->flashSession->error($resultAnotacao['message']);
        }

        $this->response = new Response();
        $this->response->setJsonContent($resultAnotacao['status'],'utf8');
        $this->response->send();

    }
}