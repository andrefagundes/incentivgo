<?php

namespace Empresa\Controllers;

use Phalcon\Paginator\Adapter\Model as Paginator;
use Incentiv\Models\Ideia,
    Incentiv\Models\Perfil;

/**
 * Empresa\Controllers\Empresa_IdeiaController
 * Classe para gerenciar ideias
 */
class EmpresaIdeiaController extends ControllerBase {

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

    public function ideiaAction() {
    }
    
    public function pesquisarIdeiaAction() {

        $this->disableLayoutBefore();

        $objIdeia = new \stdClass();

        $objIdeia->ativo  = $this->request->getPost("ativo");
        $objIdeia->filter = $this->request->getPost("filter");

        $resultIdeias = Ideia::build()->fetchAllIdeias($objIdeia);

        $numberPage = $this->request->getPost("page");
        $paginator  = new Paginator(array(
            "data"  => $resultIdeias,
            "limit" => 1,
            "page"  => $numberPage
        ));

        $this->view->page = $paginator->getPaginate();
        $this->view->pick("empresa_ideia/pesquisar-ideia");
    }
    
    public function modalIdeiaAction(){
        
        $this->disableLayoutBefore();
        
        $resultIdeia  = Ideia::build()->findFirst($this->dispatcher->getParam('code'));
        $perfis         = Perfil::build()->find('id != '.Perfil::ADMINISTRADOR_INCENTIV);
        
        $this->view->setVar("id",$resultIdeia->id);
        $this->view->setVar("perfilId", (!empty($resultIdeia->perfilId))? $resultIdeia->perfilId : Perfil::COLABORADOR);
        $this->view->setVar("nome", $resultIdeia->nome);
        $this->view->setVar("email",$resultIdeia->email);
        $this->view->setVar("perfis",$perfis);
    }
    
    public function salvarIdeiaAction() {
        $this->view->disable(); 
        if ($this->request->isPost()) {
            
            $dados  = new \stdClass();
            
            $dados->id       = $this->request->getPost('id');
            $dados->nome     = $this->request->getPost('nome', 'striptags');
            $dados->email    = $this->request->getPost('email', 'email');
            $dados->perfilId = (int) $this->request->getPost('perfilId', 'int');

            $resultIdeia = Ideia::build()->salvarIdeia($dados);
          
            if($resultIdeia['status'] == 'ok')
            {
                $this->flashSession->success($resultIdeia['message']);
            }else{
                $this->flashSession->error($resultIdeia['message']);
            }
            
            $this->response->redirect('empresa/ideia');
        }else{
            $this->response->redirect('empresa/ideia');
        }
    }
    
    public function ativarInativarIdeiaAction() {
        $this->view->disable();
        
        $dados = new \stdClass();
        $dados->status  = $this->dispatcher->getParam('status');
        $dados->id      = $this->dispatcher->getParam('id');

        $result = Ideia::build()->ativarInativarIdeia($dados);

        if($result['status'] == 'ok')
        {
            $this->flashSession->success($result['message']);
        }else{
            $this->flashSession->error($result['message']);
        }

        $this->response->redirect('empresa/ideia');
    }

}