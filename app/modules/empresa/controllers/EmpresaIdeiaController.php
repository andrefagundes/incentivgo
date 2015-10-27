<?php

namespace Empresa\Controllers;

use Phalcon\Paginator\Adapter\Model as Paginator;
use Incentiv\Models\Ideia,
    Incentiv\Models\IdeiaPontuacao,
    Incentiv\Models\Perfil;

/**
 * Empresa\Controllers\Empresa_IdeiaController
 * Classe para gerenciar ideias
 */
class EmpresaIdeiaController extends ControllerBase {

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
            $this->view->count_ideias      = Ideia::build()->count("status = 'Y' AND empresaId = {$this->_auth['empresaId']}");
            $this->view->setTemplateAfter('private-empresa');
        }
        parent::initialize();
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
        
        $objIdeia->empresaId    = $this->_auth['empresaId'];
        $objIdeia->ativo        = $this->request->getPost("ativo");
        $objIdeia->filter       = $this->request->getPost("filter");

        $resultIdeias = Ideia::build()->fetchAllIdeias($objIdeia);
        $numberPage = $this->request->getPost("page");
        $paginator  = new Paginator(array(
            "data"  => $resultIdeias,
            "limit" => 4,
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

        $result = Ideia::build()->guardarAprovarIdeia($dados);

        if($result['status'] == 'ok')
        {
            $this->flashSession->success($result['message']);
        }else{
            $this->flashSession->error($result['message']);
        }

        $this->response->redirect('empresa/ideia');
    }
    
    public function mapearPontuacaoAction(){
        $this->disableLayoutBefore();
        
        if ($this->request->isPost()) {
            
            $objMapeamento = new \stdClass();
            $objMapeamento->dados               = $this->request->getPost('dados');
            $objMapeamento->dados['empresaId']    = $this->_auth['empresaId'];

            $result = IdeiaPontuacao::build()->salvarMapeamentoIdeia($objMapeamento);
            
            if ($result['status'] == 'ok') {
                $this->flashSession->success($result['message']);
            } else {
                $this->flashSession->error($result['message']);
            }
            
            $this->response->redirect('empresa/ideia');
        }

        $resultMapeamento  = IdeiaPontuacao::build()->findFirst("empresaId = {$this->_auth['empresaId']} AND ativo = '".IdeiaPontuacao::NOT_DELETED."'");

        $this->view->setVar("id",$resultMapeamento->id);
        $this->view->setVar("pontuacao_ideia_enviada",$resultMapeamento->pontuacaoIdeiaEnviada);
        $this->view->setVar("pontuacao_ideia_aprovada",$resultMapeamento->pontuacaoIdeiaAprovada);

    }

}