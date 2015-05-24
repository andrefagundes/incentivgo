<?php

namespace Colaborador\Controllers;

use Phalcon\Paginator\Adapter\Model as Paginator,
    Phalcon\Http\Response;
use Incentiv\Models\Recompensa,
    Incentiv\Models\UsuarioPontuacaoCredito,
    Incentiv\Models\UsuarioPedidoRecompensa;

/**
 * Colaborador\Controllers\RecompensaController
 * CRUD para gerenciar recompensas
 */
class RecompensaController extends ControllerBase {

    private $_auth;
    
    public function initialize() {
        $this->_auth = $this->auth->getIdentity(); 
        if (!$this->request->isAjax()) {
            $this->view->count_pontuacao   = UsuarioPontuacaoCredito::build()->buscarPontuacaoUsuario($this->_auth['id']);
            $this->view->usuario_logado    = $this->auth->getName();
            $this->view->id                = $this->_auth['id'];
            $this->view->empresaId         = $this->_auth['empresaId'];
            $this->view->avatar            = $this->_auth['avatar'];
            $this->view->setTemplateBefore('private-colaborador');
        }
    }
    /**
     * Action padrão, mostra o formulário de busca
     */
    public function indexAction() {
        
    }

    public function recompensaAction() {
        
        
    }
    
    public function pesquisarRecompensaAction() {

        $this->disableLayoutBefore();
        
        $objDesafio = new \stdClass();
        $objDesafio->ativo      = $this->request->getPost("ativo");
        $objDesafio->filter     = $this->request->getPost("filter");

        $resultRecompensas = Recompensa::build()->fetchAllRecompensas($objDesafio);
        
        $numberPage = $this->request->getPost("page");
        $paginator = new Paginator(array(
            "data" => $resultRecompensas,
            "limit" => 4,
            "page" => $numberPage
        ));

        $this->view->page = $paginator->getPaginate();
        $this->view->pick("colaborador/pesquisar-recompensa");
    }
    
    public function modalRecompensaAction(){
        $this->disableLayoutBefore();
        
        $objPedido = new \stdClass();
        $objPedido->empresaId = $this->_auth['empresaId'];
        $objPedido->usuarioId = $this->_auth['id'];
        
        $resultPedidosRecompensa  = UsuarioPedidoRecompensa::build()->fetchAllPedidosRecompensa($objPedido);
        $recompensas = Recompensa::build()->find(array("empresaId = {$this->_auth['empresaId']} AND ativo = 'Y'",'columns'=>'id, recompensa, pontuacao'));

        foreach ($recompensas as $recompensa) {
           $recompensas_disponiveis[$recompensa->id] = '<strong>'.$recompensa->recompensa.'</strong> ( '.$recompensa->pontuacao." incentivs )"; 
        }

        $resultPedidosRecompensa->cadastroDt = date('d/m/Y H:i:s',$resultPedidosRecompensa->cadastroDt);

        $this->view->setVar('pedidosRecompensas',$resultPedidosRecompensa);
        $this->view->setVar('recompensas',$recompensas_disponiveis);
    }
    
    public function salvarPedidoRecompensaAction() {
        $this->view->disable(); 
        if ($this->request->isPost()) {
            
            $dados['recompensaId']      = $this->request->getPost('recompensaId');
            $dados['observacaoUsuario'] = $this->request->getPost('observacaoUsuario');
            $dados['usuarioId']         = $this->_auth['id'];
            $dados['empresaId']         = $this->_auth['empresaId'];

            $resultPedido = UsuarioPedidoRecompensa::build()->salvarPedidoRecompensa($dados);
      
            if($resultPedido['status'] == 'ok')
            {
                $this->flashSession->success($resultPedido['message']);
            }else{
                $this->flashSession->error($resultPedido['message']);
            }
            
            $this->response = new Response();
            $this->response->setJsonContent($resultPedido['status'],'utf8');
            $this->response->send();
        }
    }

}