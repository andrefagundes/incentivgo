<?php

namespace Empresa\Controllers;

use Phalcon\Paginator\Adapter\Model as Paginator,
    Phalcon\Http\Response;
use Incentiv\Models\Usuario,
    Incentiv\Models\Regra,
    Incentiv\Models\Desafio;

/**
 * Empresa\Controllers\EmpresaController
 * CRUD para gerenciar empresas
 */
class EmpresaController extends ControllerBase {

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

    public function campanhasAction() { }
    
    public function pesquisarCampanhasAction() {

        $this->disableLayoutBefore();
        
        $objCampanha = new \stdClass();

        $resultRegrasCampanhas = Regra::build()->fetchAllRegras($objCampanha);

        $this->view->campanhas = $resultRegrasCampanhas;
        $this->view->pick("empresa/pesquisar-campanhas");
    }
    
    public function criarCampanhaAction() {
        if ($this->request->isPost()) {
            
            $dados = $this->request->getPost('dados');
            
            $resultCadastro = Regra::build()->salvarRegra($dados);
            
            die(var_dump($resultCadastro));
            
        }else{
            $this->view->nome = 'Incentiv Iesb';
        }
    }
    public function desafiosAction() { }
    
    public function pesquisarDesafiosAction() {

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
        $this->view->pick("empresa/pesquisar-desafios");
    }
    
    public function pesquisarColaboradoresDesafioAction() {

        $this->disableLayoutBefore();
        
        $objUsuario = new \stdClass();
        $objUsuario->filter         = $this->request->get("filter");
        $objUsuario->colaboradores  = $this->request->get("colaboradores");
        $objUsuario->perfil         = Usuario::COLABORADOR;
        $objUsuario->ativo          = Usuario::NOT_DELETED;

        $resultUsuarios = Usuario::build()->fetchAllUsuariosDesafio($objUsuario);

        $this->response = new Response();
        $this->response->setJsonContent($resultUsuarios,'utf8');
        $this->response->send();
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
            
            $this->response->redirect('empresa/desafios');
        }else{
            $this->response->redirect('empresa/desafios');
        }
    }

    public function colaboradoresAction() {
        
    }
    
    public function pesquisarColaboradoresAction() {

        $this->disableLayoutBefore();

        $objUsuario = new \stdClass();

        $objUsuario->ativo  = $this->request->getPost("ativo");
        $objUsuario->filter = $this->request->getPost("filter");
        $objUsuario->perfil = 3;

        $resultUsers = Usuario::build()->fetchAllUsuarios($objUsuario);

        $numberPage = $this->request->getPost("page");
        $paginator  = new Paginator(array(
            "data"  => $resultUsers,
            "limit" => 1,
            "page"  => $numberPage
        ));

        $this->view->page = $paginator->getPaginate();
        $this->view->pick("empresa/pesquisar-colaboradores");
    }

    public function eventosAction() {
        
    }

}