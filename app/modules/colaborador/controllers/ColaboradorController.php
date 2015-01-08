<?php

namespace Colaborador\Controllers;

use Phalcon\Http\Response;
use Incentiv\Models\DesafioUsuario,
    Incentiv\Models\Usuario,
    Incentiv\Models\Ajuda,
    Incentiv\Models\Anotacao,
    Incentiv\Models\Noticia;

class ColaboradorController extends ControllerBase
{
    public function initialize()
    {
        $auth = $this->auth->getIdentity();

        $this->view->count_desafios    = DesafioUsuario::build()->count("usuarioId = ".$auth['id']." AND ( usuarioResposta IS NULL OR usuarioResposta != 'N' ) AND envioAprovacaoDt IS NULL");
        $this->view->count_ajudas      = Ajuda::count("ativo = 'Y' AND ajudaId IS NULL");
        $this->view->count_anotacoes   = Anotacao::count("usuarioId = ".$auth['id']);
        $this->view->count_noticias    = Noticia::count("empresaId = ".$auth['empresaId']);
        $this->view->usuario_logado    = $this->auth->getName();
        if (!$this->request->isAjax()) {
            $this->view->setTemplateBefore('private-colaborador');
        }
    }
    
    public function indexAction(){
//        if ($this->request->isAjax()) {
//            $this->disableLayoutBefore();
//        }
    }
    
    public function modalNoticiasAction(){
        $this->disableLayoutBefore();

        $resultNoticias  = Noticia::build()->find(array("order" => "id"));
        $this->view->noticias   = $resultNoticias; 
    }
    public function modalAnotacoesAction(){
        $this->disableLayoutBefore();
        $auth = $this->auth->getIdentity();

        $resultAnotacoes  = Anotacao::build()->find(array("usuarioId = ".$auth['id'], "order" => "id"));
        $this->view->anotacoes   = $resultAnotacoes; 
    }
    public function salvarAnotacaoAction(){
        $this->disableLayoutBefore();
        
        $auth = $this->auth->getIdentity();
        
        $objAnotacao = new \stdClass();
        $objAnotacao->descricao    = $this->request->getPost("descricao");
        $objAnotacao->usuarioId    = $auth['id'];

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
    
    public function chatAction(){
        $this->disableLayoutBefore();
        $auth = $this->auth->getIdentity();
        $this->view->usuario_id        = $auth['id'];
        $this->view->usuarios          = Usuario::build()->find(array("empresaId = ".$auth['empresaId']." AND ativo = 'Y'",'columns' =>'id,nome,email'))->toArray();     
    }
}