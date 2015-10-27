<?php

namespace Empresa\Controllers;

use Phalcon\Paginator\Adapter\Model as Paginator;
use \Incentiv\Models\Recompensa,
    Incentiv\Models\Usuario,
    Incentiv\Models\Perfil,
    Incentiv\Models\UsuarioPontuacaoDebito,
    Incentiv\Models\UsuarioPedidoRecompensa;

/**
 * Empresa\Controllers\RecompensaController
 * CRUD para gerenciar empresas
 */
class EmpresaRecompensaController extends ControllerBase {

    private $_auth;
    
    public function initialize() {
        $this->_auth = $this->auth->getIdentity(); 
        $this->view->perfilAdmin     = Perfil::ADMINISTRADOR;
        $this->view->perfilId        = $this->_auth['perfilId'];
        if (!$this->request->isAjax()) {
            $this->view->usuario_logado    = $this->auth->getName();
            $this->view->id                = $this->_auth['id'];
            $this->view->empresaId         = $this->_auth['empresaId'];
            $this->view->avatar            = $this->_auth['avatar'];
            $this->view->setTemplateBefore('private-empresa');
        }
        parent::initialize();
    }
    /**
     * Action padrão, mostra o formulário de busca
     */
    public function indexAction() {
        
    }

    public function recompensaAction() { }
    
    public function pesquisarRecompensaAction() {

        $this->disableLayoutBefore();
        
        $objDesafio = new \stdClass();
        $objDesafio->ativo      = $this->request->getPost("ativo");
        $objDesafio->filter     = $this->request->getPost("filter");
        $objDesafio->empresaId  = $this->_auth['empresaId'];

        $resultRecompensas = Recompensa::build()->fetchAllRecompensas($objDesafio);
        
        $numberPage = $this->request->getPost("page");
        $paginator = new Paginator(array(
            "data" => $resultRecompensas,
            "limit" => 4,
            "page" => $numberPage
        ));

        $this->view->page = $paginator->getPaginate();
        $this->view->pick("empresa_recompensa/pesquisar-recompensa");
    }
    
    public function modalRecompensaAction(){
        
        $this->disableLayoutBefore();

        $resultRecompensa = Recompensa::build()->findFirst($this->dispatcher->getParam('code'));

        $this->view->setVar("id",       $resultRecompensa->id);
        $this->view->setVar("recompensa",    $resultRecompensa->recompensa);
        $this->view->setVar("pontuacao",    $resultRecompensa->pontuacao);
        $this->view->setVar("observacao",$resultRecompensa->observacao);
    }
    
    public function salvarRecompensaAction() {
        $this->view->disable(); 
        if ($this->request->isPost()) {
            
            $dados  = $this->request->getPost('dados');
            $dados['empresaId'] = $this->_auth['empresaId'];
            
            $resultCadastro = Recompensa::build()->salvarRecompensa($dados);
      
            if($resultCadastro['status'] == 'ok')
            {
                $this->flashSession->success($resultCadastro['message']);
            }else{
                $this->flashSession->error($resultCadastro['message']);
            }
            
            $this->response->redirect('empresa/recompensa');
        }else{
            $this->response->redirect('empresa/recompensa');
        }
    }
    
    public function ativarInativarRecompensaAction() {
        $this->view->disable();
        
        $dados = new \stdClass();
        $dados->status  = $this->dispatcher->getParam('status');
        $dados->id      = $this->dispatcher->getParam('id');

        $resultCadastro = Recompensa::build()->ativarInativarRecompensa($dados);

        if($resultCadastro['status'] == 'ok')
        {
            $this->flashSession->success($resultCadastro['message']);
        }else{
            $this->flashSession->error($resultCadastro['message']);
        }

        $this->response->redirect('empresa/recompensa');
    }
    
    public function utilizarRecompensaAction(){
        $usuarios = Usuario::build()->find(array("empresaId = {$this->_auth['empresaId']} AND ativo = 'Y'",'columns'=>'id,nome'));
        $recompensas = Recompensa::build()->find(array("empresaId = {$this->_auth['empresaId']} AND ativo = 'Y'",'columns'=>'id,recompensa'));
        
        $this->view->setVar('usuarios',$usuarios);
        $this->view->setVar('recompensas',$recompensas);
    }
    
    public function debitarRecompensaAction(){
        $this->view->disable(); 
        if ($this->request->isPost()) {
            
            $dados  = $this->request->getPost('dados');
            $dados['empresa_id'] = $this->_auth['empresaId'];

            $resultCadastro = UsuarioPontuacaoDebito::build()->debitarUsuario($dados);
      
            if($resultCadastro['status'] == 'ok')
            {
                $this->flashSession->success($resultCadastro['message']);
            }else{
                $this->flashSession->error($resultCadastro['message']);
            }
            
            $this->response->redirect('empresa/recompensa/utilizar-recompensa');
        }else{
            $this->response->redirect('empresa/recompensa/utilizar-recompensa');
        }
    }
    
    public function verPedidosAction(){
        
    }
    
     public function pesquisarPedidosAction() {

        $this->disableLayoutBefore();
        
        $auth = $this->auth->getIdentity();

        $objPedidos = new \stdClass();
        $objPedidos->ativo      = $this->request->getPost("ativo");
        $objPedidos->filter     = $this->request->getPost("filter");
        $objPedidos->empresaId  = $auth['empresaId'];

        $resultPedidosRecompensa = UsuarioPedidoRecompensa::build()->fetchAllPedidos($objPedidos);

        $numberPage = $this->request->getPost("page");
        $paginator = new Paginator(array(
            "data" => $resultPedidosRecompensa,
            "limit" => 4,
            "page" => $numberPage
        ));

        $this->view->page = $paginator->getPaginate();
        $this->view->pick("empresa_recompensa/pesquisar-pedidos");
    }
    
    public function resultadoUsoIcentivAction() {
        $this->view->disable();
        
        $objDadosPedido = new \stdClass();
        $objDadosPedido->resposta        = $this->dispatcher->getParam('status');
        $objDadosPedido->id_recompensa   = $this->dispatcher->getParam('id_recompensa');

        $result = UsuarioPedidoRecompensa::build()->alterarStatusPedidoRecompensa($objDadosPedido);

        if($result['status'] == 'ok')
        {
            $this->flashSession->success($result['message']);
        }else{
            $this->flashSession->error($result['message']);
        }

        $this->response->redirect('empresa/recompensa/ver-pedidos-recompensa');
    }
}