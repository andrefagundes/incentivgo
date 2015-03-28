<?php

namespace Empresa\Controllers;

use Phalcon\Paginator\Adapter\Model as Paginator;

use Incentiv\Models\Mensagem,
    Incentiv\Models\MensagemDestinatario,
    Incentiv\Models\MensagemExcluida;

/**
 * Empresa\Controllers\EmpresaController
 * CRUD para gerenciar empresas
 */
class EmpresaMensagemController extends ControllerBase {
    
    private $_auth;

    public function initialize() {
        $this->_auth = $this->auth->getIdentity();
        if (!$this->request->isAjax()) {
            $this->view->count_mensagens_recebidas = MensagemDestinatario::build()->quantMensagensRecebidas($this->_auth['id']);
            $this->view->count_mensagens_enviadas  = Mensagem::build()->quantMensagensEnviadas($this->_auth['id']);
            $this->view->count_mensagens_excluidas = MensagemExcluida::build()->count("usuarioId = {$this->_auth['id']}");
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
        $objMensagem->remetenteId       = $this->_auth['id'];
        $objMensagem->tipo              = $this->request->getPost("tipo");
        $objMensagem->filter            = $this->request->getPost("filter");

        if($this->request->getPost("tipo") == Mensagem::MENSAGENS_TIPO_EXCLUIDA ){
            $resultMensagens = Mensagem::build()->fetchAllMensagensExcluidas($objMensagem);
        }else{
            $resultMensagens = Mensagem::build()->fetchAllMensagens($objMensagem);
        }
     
        $numberPage = $this->request->getPost("page");
        $paginator  = new Paginator(array(
            "data"  => $resultMensagens,
            "limit" => 5,
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
    
    public function lerMensagemAction(){
         
        $this->disableLayoutBefore();

        $resultMensagem = Mensagem::build()->findFirst($this->dispatcher->getParam('code'));
        
        
        $objDadosMensagemLida = new \stdClass();
        $objDadosMensagemLida->mensagemId       = $resultMensagem->id;
        $objDadosMensagemLida->destinatarioId   = $this->_auth['id'];
        
        MensagemDestinatario::build()->setarMensagemLida($objDadosMensagemLida);
        
        $usuariosDestinatarios = '';
        foreach ($resultMensagem->mensagemDestinatario as $destinatario){
            $usuariosDestinatarios .= $destinatario->usuario->nome." (".$destinatario->usuario->email."), ";
        }

        $this->view->setVar("id",              $resultMensagem->id);
        $this->view->setVar("titulo",          $resultMensagem->titulo);
        $this->view->setVar("mensagem",        $resultMensagem->mensagem);
        $this->view->setVar("avatar",          $resultMensagem->usuarioRemetente->avatar);
        $this->view->setVar("nomeRemetente",   $resultMensagem->usuarioRemetente->nome);
        $this->view->setVar("emailRemetente",  $resultMensagem->usuarioRemetente->email);
        $this->view->setVar("envioDt", date('d/m/Y H:m:s',strtotime($resultMensagem->envioDt))   );
        $this->view->setVar("envioDtBanco", $resultMensagem->envioDt);
        $this->view->setVar("usuariosDestinatarios",  substr($usuariosDestinatarios,0, strlen($usuariosDestinatarios)-2));
        $this->view->pick("empresa_mensagem/ler-mensagem");
    }
    
    public function excluirMensagemAction(){
        $this->disableLayoutBefore();
        
        $objMensagem        = new \stdClass();
        $objMensagem->usuarioId  = $this->_auth['id'];
        $objMensagem->mensagemId = $this->request->getPost("id");

        $resultMensagem = MensagemExcluida::build()->excluirMensagem($objMensagem);

        if($resultMensagem['status'] == 'ok')
        {
            $this->flashSession->success($resultMensagem['message']);
        }else{
            $this->flashSession->error($resultMensagem['message']);
        }

//        $this->response = new Response();
//        $this->response->setJsonContent($resultMensagem['status'],'utf8');
//        $this->response->send();
        $this->response->redirect('empresa/mensagens');
    }

}