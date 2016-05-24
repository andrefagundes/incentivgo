<?php

namespace Colaborador\Controllers;

use Phalcon\Paginator\Adapter\Model as Paginator,
    Phalcon\Http\Response;

use Incentiv\Models\Usuario,
    Incentiv\Models\Mensagem,
    Incentiv\Models\MensagemDestinatario,
    Incentiv\Models\MensagemExcluida,
    Incentiv\Models\UsuarioPontuacaoCredito;

/**
 * Colaborador\Controllers\MensagemController
 * Classe para gerenciar mensagens no perfil colaborador
 */
class MensagemController extends ControllerBase {
    
    private $_auth;
    private $_lang;

    public function initialize() {
        $this->_auth = $this->auth->getIdentity();
        $this->view->count_mensagens_recebidas = MensagemDestinatario::build()->quantMensagensRecebidas($this->_auth['id']);
        $this->view->count_mensagens_enviadas  = Mensagem::build()->quantMensagensEnviadas($this->_auth['id']);
        $this->view->count_mensagens_excluidas = MensagemExcluida::build()->count("usuarioId = {$this->_auth['id']}");

        if (!$this->request->isAjax()) {
            $this->view->count_pontuacao   = UsuarioPontuacaoCredito::build()->buscarPontuacaoUsuario($this->_auth['id']);
            $this->view->usuario_logado    = $this->auth->getName();
            $this->view->id                = $this->_auth['id'];
            $this->view->empresaId         = $this->_auth['empresaId'];
            $this->view->avatar            = $this->_auth['avatar'];
            $this->view->setTemplateBefore('private-colaborador');
        }
        $this->_lang = parent::initialize();
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
        $this->view->pick("mensagem/pesquisar-mensagem");
    }
    
    public function novaMensagemAction(){
        $this->disableLayoutBefore();
        $this->view->pick("mensagem/nova-mensagem");
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
            
            $this->response->redirect('colaborador/mensagens');
        }else{
            $this->response->redirect('colaborador/mensagens');
        }
    }
    
    public function responderMensagemAction(){
        $this->view->disable(); 
        if ($this->request->isPost()) {
            
            $dados['titulo']        = $this->request->getPost('resposta-titulo');
            $dados['mensagemId']    = $this->request->getPost('id');
            $dados['mensagem']      = $this->request->getPost('resposta-mensagem');
            $dados['remetenteId']     = $this->_auth['id'];

            
            $resultMensagem = Mensagem::build()->salvarMensagemResposta($dados);
          
            if($resultMensagem['status'] == 'ok')
            {
                $this->flashSession->success($resultMensagem['message']);
            }else{
                $this->flashSession->error($resultMensagem['message']);
            }
            
            $this->response->redirect('colaborador/mensagens');
        }else{
            $this->response->redirect('colaborador/mensagens');
        }
    }
    
    public function lerMensagemAction(){
         
        $this->disableLayoutBefore();
        
        //diz se usuario pode ou não responder mensagem.
        $permissao = false;
        
        $mensagemId = $this->dispatcher->getParam('code');
        
        $resultMensagem  = Mensagem::build()->find(array("(id = {$mensagemId} OR mensagemId = {$mensagemId})", "order" => "id ")); 
        
        $objDadosMensagemLida = new \stdClass();
        $objDadosMensagemLida->mensagemId       = $this->dispatcher->getParam('code');
        $objDadosMensagemLida->destinatarioId   = $this->_auth['id'];
        
        MensagemDestinatario::build()->setarMensagemLida($objDadosMensagemLida);
        
        
        $mensagens = '';
        foreach ($resultMensagem as $key => $mensagem){
            
            $data_envio_formatada = $this->di->get('funcoes')->formatarDataHoraSaida($mensagem->envioDt,$this->_lang['lang']);
            
            $mensagens[$key]['id']              = $mensagem->id;
            $mensagens[$key]['titulo']          = $mensagem->titulo;
            $mensagens[$key]['mensagem']        = $mensagem->mensagem;
            $mensagens[$key]['avatar']          = $mensagem->usuarioRemetente->avatar;
            $mensagens[$key]['nomeRemetente']   = $mensagem->usuarioRemetente->nome;
            $mensagens[$key]['emailRemetente']  = $mensagem->usuarioRemetente->email;
            $mensagens[$key]['envioDt']         = $data_envio_formatada;
            $mensagens[$key]['envioDtBanco']    = $mensagem->envioDt;
            $mensagens[$key]['id_mensagem_pai'] = $mensagemId;
            
            $usuariosDestinatarios = '';
            foreach ($mensagem->mensagemDestinatario as $destinatario){
                $usuariosDestinatarios .= $destinatario->usuario->nome." (".$destinatario->usuario->email."), ";
            }
            
            $mensagens[$key]['usuariosDestinatarios'] = substr($usuariosDestinatarios,0, strlen($usuariosDestinatarios)-2);
        }
        
        $mensagemPai = Mensagem::build()->findFirst(array("id = {$mensagemId}"));
        
        if($mensagemPai->remetenteId != $this->_auth['id']){
                $permissao = true;
        }
        
        $this->view->setVar("resposta",       $permissao);
        $this->view->setVar("mensagens",       $mensagens);
        $this->view->setVar("empresaId",       $this->_auth['empresaId']);
        $this->view->pick("mensagem/ler-mensagem");
    }
    
    public function verificarMensagensAction(){
        $this->disableLayoutBefore();
        $mensagensRecebidas = Mensagem::build()->buscarMensagensRecebidas($this->_auth['id']);
        $this->response = new Response();
        $this->response->setJsonContent($mensagensRecebidas,'utf8');
        $this->response->send();
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

        $this->response = new Response();
        $this->response->setJsonContent(array('status'=>$resultMensagem['status']));
        $this->response->send();
    }
    
    public function pesquisarColaboradoresMensagemAction() {

        $this->disableLayoutBefore();
        
        $objUsuario = new \stdClass();
        $objUsuario->filter         = $this->request->get("filter");
        $objUsuario->colaboradores  = $this->request->get("colaboradores");
        $objUsuario->usuarioLogado  = $this->_auth['id'];
        $objUsuario->empresaLogada  = $this->_auth['empresaId'];
        $objUsuario->ativo          = Usuario::NOT_DELETED;

        $resultUsuarios = Usuario::build()->fetchAllUsuariosDesafio($objUsuario);

        $this->response = new Response();
        $this->response->setJsonContent($resultUsuarios,'utf8');
        $this->response->send();
    }
}