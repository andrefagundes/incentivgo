<?php

namespace Empresa\Controllers;

use Phalcon\Paginator\Adapter\Model as Paginator;

use Incentiv\Models\Desafio,
    Incentiv\Models\Usuario,
    Incentiv\Models\Mensagem,
    Incentiv\Models\UsuarioPontuacaoCredito as Credito;

/**
 * Empresa\Controllers\EmpresaController
 * CRUD para gerenciar empresas
 */
class EmpresaMensagemController extends ControllerBase {
    
    private $_auth;

    public function initialize() {
        $this->_auth = $this->auth->getIdentity();
        if (!$this->request->isAjax()) {
//            $this->view->count_mensagens_recebidas = Mensagem::build()->count("destinatarioId = ".$this->_auth['id']);
//            $this->view->count_mensagens_enviadas  = Usuario::build()->count("remetenteId = ".$this->_auth['empresaId']);
//            $this->view->count_mensagens_excluidas = Usuario::build()->count("ativo = 'N'");
            $this->view->usuario_logado    = $this->auth->getName();
            $this->view->id                = $this->_auth['id'];
            $this->view->empresaId         = $this->_auth['empresaId'];
            $this->view->avatar            = $this->_auth['avatar'];
            $this->view->setTemplateBefore('private-empresa');
        }
    }
    
    public function mensagemAction(){
        
    }

    public function pesquisarMensagemAction() {

        $this->disableLayoutBefore();

        $objMensagem = new \stdClass();

        $objMensagem->destinatarioId    = $this->_auth['id'];
        $objMensagem->ativo             = $this->request->getPost("ativo");
        $objMensagem->filter            = $this->request->getPost("filter");

        $resultMensagens = Mensagem::build()->fetchAllMensagens($objMensagem);
        $numberPage = $this->request->getPost("page");
        $paginator  = new Paginator(array(
            "data"  => $resultMensagens,
            "limit" => 3,
            "page"  => $numberPage
        ));

        $this->view->page = $paginator->getPaginate();
        $this->view->pick("empresa_mensagem/pesquisar-mensagem");
    }
    
    public function novaMensagemAction(){
        $this->disableLayoutBefore();
        $this->view->pick("empresa_mensagem/nova-mensagem");
    }
    
    public function salvarMensagemAction(){
        $this->view->disable(); 
        if ($this->request->isPost()) {
            
            $dados  = $this->request->getPost('dados');
            $dados['remetente'] = $this->_auth['id'];
  
            $resultMensagem = Mensagem::build()->salvarMensagem($dados);
          
            if($resultMensagem['status'] == 'ok')
            {
                $this->flashSession->success($resultMensagem['message']);
            }else{
                $this->flashSession->error($resultMensagem['message']);
            }
            
            $this->response->redirect('empresa/mensagens');
        }else{
            $this->response->redirect('empresa/mensagens');
        }
    }
}