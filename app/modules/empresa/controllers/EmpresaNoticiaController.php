<?php

namespace Empresa\Controllers;

use Phalcon\Paginator\Adapter\Model as Paginator;
use Incentiv\Models\Noticia,
    Incentiv\Models\Perfil;

/**
 * Empresa\Controllers\Empresa_NoticiaController
 * Classe para gerenciar notícias
 */
class EmpresaNoticiaController extends ControllerBase {

    private $_auth;
    private $_lang;
    
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
       $this->_lang = parent::initialize();
    }
    /**
     * Action padrão, mostra o formulário de busca
     */
    public function indexAction() {
        
    }
    
    public function noticiaAction() { }
    
    public function pesquisarNoticiaAction() {

        $this->disableLayoutBefore();

        $objNoticia = new \stdClass();
        $objNoticia->ativo      = $this->request->getPost("ativo");
        $objNoticia->filter     = $this->request->getPost("filter");
        $objNoticia->empresaId  = $this->_auth['empresaId'];

        $resultRegrasNoticias = Noticia::build()->fetchAllNoticias($objNoticia);

        $numberPage = $this->request->getPost("page");
        $paginator = new Paginator(array(
            "data" => $resultRegrasNoticias,
            "limit" => 4,
            "page" => $numberPage
        ));

        $this->view->page = $paginator->getPaginate();
        $this->view->pick("empresa_noticia/pesquisar-noticia");
    }
    
    public function modalNoticiaAction(){
        
        $this->disableLayoutBefore();

        $resultNoticia = Noticia::build()->findFirst($this->dispatcher->getParam('code'));

        $this->view->setVar("id",       $resultNoticia->id);
        $this->view->setVar("titulo",   $resultNoticia->titulo);
        $this->view->setVar("noticia",  $resultNoticia->noticia);

    }
    
//    public function pesquisarColaboradoresNoticiaAction() {
//
//        $this->disableLayoutBefore();
//        
//        $objUsuario = new \stdClass();
//        $objUsuario->filter         = $this->request->get("filter");
//        $objUsuario->colaboradores  = $this->request->get("colaboradores");
//        $objUsuario->perfil         = Perfil::COLABORADOR;
//        $objUsuario->ativo          = Usuario::NOT_DELETED;
//
//        $resultUsuarios = Usuario::build()->fetchAllUsuariosNoticia($objUsuario);
//
//        $this->response = new Response();
//        $this->response->setJsonContent($resultUsuarios,'utf8');
//        $this->response->send();
//    }
    
    public function salvarNoticiaAction() {
        $this->view->disable(); 
        if ($this->request->isPost()) {
            
            $dados  = $this->request->getPost('dados');
            $dados['empresaId'] = $this->_auth['empresaId'];

            $resultCadastro = Noticia::build()->salvarNoticia($dados);
          
            if($resultCadastro['status'] == 'ok')
            {
                $this->flashSession->success($resultCadastro['message']);
            }else{
                $this->flashSession->error($resultCadastro['message']);
            }
            
            $this->response->redirect('empresa/noticia');
        }else{
            $this->response->redirect('empresa/noticia');
        }
    }
    
    public function ativarInativarNoticiaAction() {
        $this->view->disable();
        
        $dados = new \stdClass();
        $dados->status  = $this->dispatcher->getParam('status');
        $dados->id      = $this->dispatcher->getParam('id');

        $resultCadastro = Noticia::build()->ativarInativarNoticia($dados);

        if($resultCadastro['status'] == 'ok')
        {
            $this->flashSession->success($resultCadastro['message']);
        }else{
            $this->flashSession->error($resultCadastro['message']);
        }

        $this->response->redirect('empresa/noticia');
    }

}