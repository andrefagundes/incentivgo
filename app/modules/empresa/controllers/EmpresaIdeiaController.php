<?php

namespace Empresa\Controllers;

use Phalcon\Paginator\Adapter\Model as Paginator;
use Incentiv\Models\Ideia;

/**
 * Empresa\Controllers\Empresa_IdeiaController
 * Classe para gerenciar ideias
 */
class EmpresaIdeiaController extends ControllerBase {

    public function initialize() {
        if (!$this->request->isAjax()) {
            $auth = $this->auth->getIdentity();          
            $this->view->usuario_logado    = $this->auth->getName();
            $this->view->avatar            = $auth['avatar'];
            $this->view->count_ideias      = Ideia::build()->count("status = 'Y' AND empresaId = {$auth['empresaId']}");
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
        
        $auth = $this->auth->getIdentity(); 

        $objIdeia = new \stdClass();
        
        $objIdeia->empresaId    = $auth['empresaId'];
        $objIdeia->ativo        = $this->request->getPost("ativo");
        $objIdeia->filter       = $this->request->getPost("filter");

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

        $this->view->setVar("id",$resultIdeia->id);
        $this->view->setVar("descricao", $resultIdeia->descricao);
        $this->view->setVar("titulo", $resultIdeia->titulo);
        $this->view->setVar("dt_envio", $resultIdeia->criacaoDt);
        $this->view->setVar("colaborador", $resultIdeia->usuario->nome);
        
    }
    
    public function guardarAprovarIdeiaAction() {
        $this->view->disable();
        
        $dados = new \stdClass();
        $dados->id        = $this->request->getPost("id");
        $dados->resposta  = $this->request->getPost("resposta");

//die(var_dump($dados));
        $result = Ideia::build()->guardarAprovarIdeia($dados);

        if($result['status'] == 'ok')
        {
            $this->flashSession->success($result['message']);
        }else{
            $this->flashSession->error($result['message']);
        }

        $this->response->redirect('empresa/ideia');
    }

}