<?php

namespace Empresa\Controllers;

use Phalcon\Paginator\Adapter\Model as Paginator,
    Incentiv\Models\Regra;

/**
 * Empresa\Controllers\RegraController
 * CRUD para gerenciar empresas
 */
class RegraController extends ControllerBase {

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

    public function regraAction() { }
    
    public function pesquisarRegraAction() {

        $this->disableLayoutBefore();
        
        $objDesafio = new \stdClass();
        $objDesafio->ativo      = $this->request->getPost("ativo");
        $objDesafio->filter     = $this->request->getPost("filter");

        $resultRegras = Regra::build()->fetchAllRegras($objDesafio);
        
        $numberPage = $this->request->getPost("page");
        $paginator = new Paginator(array(
            "data" => $resultRegras,
            "limit" => 2,
            "page" => $numberPage
        ));

        $this->view->page = $paginator->getPaginate();
        $this->view->pick("regra/pesquisar-regra");
    }
    
    public function modalRegraAction(){
        
        $this->disableLayoutBefore();

        $resultRegra = Regra::build()->findFirst($this->dispatcher->getParam('code'));

        $this->view->setVar("id",       $resultRegra->id);
        $this->view->setVar("regra",    $resultRegra->regra);
        $this->view->setVar("pontuacao",    $resultRegra->pontuacao);
        $this->view->setVar("observacao",$resultRegra->observacao);
    }
    
    public function salvarRegraAction() {
        $this->view->disable(); 
        if ($this->request->isPost()) {
            
            $dados  = $this->request->getPost('dados');

            $resultCadastro = Regra::build()->salvarRegra($dados);
      
            if($resultCadastro['status'] == 'ok')
            {
                $this->flashSession->success($resultCadastro['message']);
            }else{
                $this->flashSession->error($resultCadastro['message']);
            }
            
            $this->response->redirect('empresa/regra');
        }else{
            $this->response->redirect('empresa/regra');
        }
    }
    
    public function ativarInativarRegraAction() {
        $this->view->disable();
        
        $dados = new \stdClass();
        $dados->status  = $this->dispatcher->getParam('status');
        $dados->id      = $this->dispatcher->getParam('id');

        $resultCadastro = Regra::build()->ativarInativarRegra($dados);

        if($resultCadastro['status'] == 'ok')
        {
            $this->flashSession->success($resultCadastro['message']);
        }else{
            $this->flashSession->error($resultCadastro['message']);
        }

        $this->response->redirect('empresa/regra');
    }
}