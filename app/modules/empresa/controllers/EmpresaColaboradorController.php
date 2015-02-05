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

    public function initialize() {

        if (!$this->request->isAjax()) {
            $auth = $this->auth->getIdentity();          
            $this->view->usuario_logado    = $this->auth->getName();
            $this->view->id                = $auth['id'];
            $this->view->empresaId         = $auth['empresaId'];
            $this->view->avatar            = $auth['avatar'];
            $this->view->setTemplateBefore('private-empresa');
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

        $objUsuario->ativo  = $this->request->getPost("ativo");
        $objUsuario->filter = $this->request->getPost("filter");
        $objUsuario->perfil = $this->request->getPost("perfil");

        $resultUsers = Usuario::build()->fetchAllUsuarios($objUsuario);

        $numberPage = $this->request->getPost("page");
        $paginator  = new Paginator(array(
            "data"  => $resultUsers,
            "limit" => 3,
            "page"  => $numberPage
        ));

        $this->view->page = $paginator->getPaginate();
        $this->view->pick("empresa_colaborador/pesquisar-colaborador");
    }
    
    public function modalColaboradorAction(){
        
        $this->disableLayoutBefore();
        
        $resultUsuario  = Usuario::build()->findFirst($this->dispatcher->getParam('code'));
        $perfis         = Perfil::build()->find('id != '.Perfil::ADMINISTRADOR_INCENTIV);
        
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
            
            $dados->id       = $this->request->getPost('id');
            $dados->nome     = $this->request->getPost('nome', 'striptags');
            $dados->email    = $this->request->getPost('email', 'email');
            $dados->perfilId = (int) $this->request->getPost('perfilId', 'int');

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