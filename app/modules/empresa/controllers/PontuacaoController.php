<?php

namespace Empresa\Controllers;

use Phalcon\Paginator\Adapter\Model as Paginator;
use Incentiv\Models\RegraPontuacao;

/**
 * Empresa\Controllers\PontuacaoController
 * CRUD para gerenciar as regras de pontuações usadas pelos colaboradores
 */
class PontuacaoController extends ControllerBase {

    public function initialize() {
        if (!$this->request->isAjax()) {
            $this->view->setTemplateBefore('private-empresa');
        }
    }
    /**
     * Action padrão, mostra o formulário de busca
     */
    public function indexAction() {
        
    }

    public function pontuacaoAction() { }
    
    public function pesquisarPontuacaoAction() {

        $this->disableLayoutBefore();
        
        $objDesafio = new \stdClass();
        $objDesafio->ativo      = $this->request->getPost("ativo");
        $objDesafio->filter     = $this->request->getPost("filter");

        $resultPontuacoes = RegraPontuacao::build()->fetchAllPontuacoes($objDesafio);
        
        $numberPage = $this->request->getPost("page");
        $paginator = new Paginator(array(
            "data" => $resultPontuacoes,
            "limit" => 2,
            "page" => $numberPage
        ));

        $this->view->page = $paginator->getPaginate();
        $this->view->pick("pontuacao/pesquisar-pontuacao");
    }
    
    public function modalPontuacaoAction(){
        
        $this->disableLayoutBefore();

        $resultPontuacao = RegraPontuacao::build()->findFirst($this->dispatcher->getParam('code'));

        $this->view->setVar("id",       $resultPontuacao->id);
        $this->view->setVar("regra",    $resultPontuacao->regra);
        $this->view->setVar("pontuacao",$resultPontuacao->pontuacao);
        $this->view->setVar("observacao",$resultPontuacao->observacao);
    }
    
    public function salvarPontuacaoAction() {
        $this->view->disable(); 
        if ($this->request->isPost()) {
            
            $dados  = $this->request->getPost('dados');

            $resultPontuacao = RegraPontuacao::build()->salvarPontuacao($dados);
      
            if($resultPontuacao['status'] == 'ok')
            {
                $this->flashSession->success($resultPontuacao['message']);
            }else{
                $this->flashSession->error($resultPontuacao['message']);
            }
            
            $this->response->redirect('empresa/pontuacao');
        }else{
            $this->response->redirect('empresa/pontuacao');
        }
    }
    
    public function ativarInativarPontuacaoAction() {
        $this->view->disable();
        
        $dados = new \stdClass();
        $dados->status  = $this->dispatcher->getParam('status');
        $dados->id      = $this->dispatcher->getParam('id');

        $resultPontuacao = RegraPontuacao::build()->ativarInativarPontuacao($dados);

        if($resultPontuacao['status'] == 'ok')
        {
            $this->flashSession->success($resultPontuacao['message']);
        }else{
            $this->flashSession->error($resultPontuacao['message']);
        }

        $this->response->redirect('empresa/pontuacao');
    }
}