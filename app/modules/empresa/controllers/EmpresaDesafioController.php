<?php

namespace Empresa\Controllers;

use Phalcon\Paginator\Adapter\Model as Paginator,
    Phalcon\Http\Response;
use Incentiv\Models\Usuario,
    Incentiv\Models\Desafio,
    Incentiv\Models\Perfil;

/**
 * Empresa\Controllers\Empresa_DesafioController
 * Classe para gerenciar desafios
 */
class EmpresaDesafioController extends ControllerBase {

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
    
    public function desafioAction() { }
    
    public function pesquisarDesafioAction() {

        $this->disableLayoutBefore();
        
        $objDesafio = new \stdClass();
        $objDesafio->ativo      = $this->request->getPost("ativo");
        $objDesafio->filter     = $this->request->getPost("filter");

        $resultRegrasDesafios = Desafio::build()->fetchAllDesafios($objDesafio);
        
        $numberPage = $this->request->getPost("page");
        $paginator = new Paginator(array(
            "data" => $resultRegrasDesafios,
            "limit" => 2,
            "page" => $numberPage
        ));

        $this->view->page = $paginator->getPaginate();
        $this->view->pick("empresa_desafio/pesquisar-desafio");
    }
    
    public function modalDesafioAction(){
        
        $this->disableLayoutBefore();

        $resultDesafio = Desafio::build()->findFirst($this->dispatcher->getParam('code'));
        
        $idsParticipantes = "";
        foreach ($resultDesafio->desafioUsuario as $participantes){
            $idsParticipantes .= $participantes->usuarioId.',';
        }

        $this->view->setVar("id",       $resultDesafio->id);
        $this->view->setVar("desafio",  $resultDesafio->desafio);
        $this->view->setVar("pontuacao",$resultDesafio->pontuacao);
        $this->view->setVar("inicioDt", (!empty($resultDesafio->inicioDt))?date('d/m/Y',strtotime($resultDesafio->inicioDt)):'');
        $this->view->setVar("fimDt",    (!empty($resultDesafio->fimDt))?date('d/m/Y',strtotime($resultDesafio->fimDt)):'');
        $this->view->setVar("premiacao",$resultDesafio->premiacao);
        $this->view->setVar("colaboradores",  substr($idsParticipantes,0, strlen($idsParticipantes)-1));

    }
    
    public function pesquisarColaboradoresDesafioAction() {

        $this->disableLayoutBefore();
        
        $objUsuario = new \stdClass();
        $objUsuario->filter         = $this->request->get("filter");
        $objUsuario->colaboradores  = $this->request->get("colaboradores");
        $objUsuario->perfil         = Perfil::COLABORADOR;
        $objUsuario->ativo          = Usuario::NOT_DELETED;

        $resultUsuarios = Usuario::build()->fetchAllUsuariosDesafio($objUsuario);

        $this->response = new Response();
        $this->response->setJsonContent($resultUsuarios,'utf8');
        $this->response->send();
    }
    
    public function salvarDesafioAction() {
        $this->view->disable(); 
        if ($this->request->isPost()) {
            
            $dados  = $this->request->getPost('dados');

            $resultCadastro = Desafio::build()->salvarDesafio($dados);
          
            if($resultCadastro['status'] == 'ok')
            {
                $this->flashSession->success($resultCadastro['message']);
            }else{
                $this->flashSession->error($resultCadastro['message']);
            }
            
            $this->response->redirect('empresa/desafio');
        }else{
            $this->response->redirect('empresa/desafio');
        }
    }
    
    public function ativarInativarDesafioAction() {
        $this->view->disable();
        
        $dados = new \stdClass();
        $dados->status  = $this->dispatcher->getParam('status');
        $dados->id      = $this->dispatcher->getParam('id');

        $resultCadastro = Desafio::build()->ativarInativarDesafio($dados);

        if($resultCadastro['status'] == 'ok')
        {
            $this->flashSession->success($resultCadastro['message']);
        }else{
            $this->flashSession->error($resultCadastro['message']);
        }

        $this->response->redirect('empresa/desafio');
    }

}