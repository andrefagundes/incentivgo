<?php

namespace Empresa\Controllers;

use Phalcon\Paginator\Adapter\Model as Paginator;
use Incentiv\Models\Usuario,
    Incentiv\Models\Perfil;

/**
 * Empresa\Controllers\Empresa_ColaboradorController
 * Classe para gerenciar colaboradores
 */
class EmpresaColaboradorController extends ControllerBase {

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
            $this->view->setTemplateAfter('private-empresa');
        }
    }
    /**
     * Action padrão, mostra o formulário de busca
     */
    public function indexAction() {
        
    }

    public function colaboradorAction() {
    }
    
    public function pesquisarColaboradorAction() {

        $this->disableLayoutBefore();

        $objUsuario = new \stdClass();

        $objUsuario->ativo      = $this->request->getPost("ativo");
        $objUsuario->filter     = $this->request->getPost("filter");
        $objUsuario->perfil     = $this->request->getPost("perfil");
        $objUsuario->empresaId  = $this->_auth['empresaId'];

        $resultUsers = Usuario::build()->fetchAllUsuarios($objUsuario);

        $numberPage = $this->request->getPost("page");
        $paginator  = new Paginator(array(
            "data"  => $resultUsers,
            "limit" => 4,
            "page"  => $numberPage
        ));

        $this->view->page = $paginator->getPaginate();
        $this->view->pick("empresa_colaborador/pesquisar-colaborador");
    }
    
    public function modalColaboradorAction(){
        
        $this->disableLayoutBefore();
        
        $resultUsuario  = Usuario::build()->findFirst($this->dispatcher->getParam('code'));
        
        if($this->_auth['perfilId'] == Perfil::ADMINISTRADOR){
            $perfis = Perfil::build()->find('id != '.Perfil::ADMINISTRADOR_INCENTIV);
        }elseif ($this->_auth['perfilId'] == Perfil::GERENTE) {
            $perfis = Perfil::build()->find('id = '.Perfil::COLABORADOR);
        }
        
        $this->view->setVar("id",$resultUsuario->id);
        $this->view->setVar("perfilId", (!empty($resultUsuario->perfilId))? $resultUsuario->perfilId : Perfil::COLABORADOR);
        $this->view->setVar("nome", $resultUsuario->nome);
        $this->view->setVar("email",$resultUsuario->email);
        $this->view->setVar("perfis",$perfis);
    }
    
    public function salvarColaboradorAction() {
        $this->view->disable(); 
        if ($this->request->isPost()) {
            
            $dados  = new \stdClass();
            
            $dados->id        = $this->request->getPost('id');
            $dados->nome      = $this->request->getPost('nome', 'striptags');
            $dados->email     = $this->request->getPost('email', 'email');
            $dados->perfilId  = (int) $this->request->getPost('perfilId', 'int');
            $dados->empresaId = $this->_auth['empresaId'];

            $resultUsuario = Usuario::build()->salvarUsuario($dados);
          
            if($resultUsuario['status'] == 'ok')
            {
                $this->flashSession->success($resultUsuario['message']);
            }else{
                $this->flashSession->error($resultUsuario['message']);
            }
            
            $this->response->redirect('empresa/colaborador');
        }else{
            $this->response->redirect('empresa/colaborador');
        }
    }
    
    public function ativarInativarColaboradorAction() {
        $this->view->disable();
        
        $dados = new \stdClass();
        $dados->status  = $this->dispatcher->getParam('status');
        $dados->id      = $this->dispatcher->getParam('id');

        $result = Usuario::build()->ativarInativarUsuario($dados);

        if($result['status'] == 'ok')
        {
            $this->flashSession->success($result['message']);
        }else{
            $this->flashSession->error($result['message']);
        }

        $this->response->redirect('empresa/colaborador');
    }

}