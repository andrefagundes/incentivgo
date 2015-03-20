<?php

namespace Empresa\Controllers;

use Phalcon\Paginator\Adapter\Model as Paginator,
    Phalcon\Http\Response;
use Incentiv\Models\Usuario,
    Incentiv\Models\Desafio,
    Incentiv\Models\DesafioUsuario,
    Incentiv\Models\Perfil;

/**
 * Empresa\Controllers\Empresa_DesafioController
 * Classe para gerenciar desafios
 */
class EmpresaDesafioController extends ControllerBase {

    public function initialize() {
        if (!$this->request->isAjax()) {
            $auth = $this->auth->getIdentity();          
            $this->view->usuario_logado    = $this->auth->getName();
            $this->view->avatar            = $auth['avatar'];
            $this->view->id                = $auth['id'];
            $this->view->empresaId         = $auth['empresaId'];
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
            "limit" => 3,
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
        $this->view->setVar("tipo",     $resultDesafio->tipo);
        $this->view->setVar("inicioDt", (!empty($resultDesafio->inicioDt))?date('d/m/Y',strtotime($resultDesafio->inicioDt)):'');
        $this->view->setVar("fimDt",    (!empty($resultDesafio->fimDt))?date('d/m/Y',strtotime($resultDesafio->fimDt)):'');
        $this->view->setVar("premiacao",$resultDesafio->premiacao);
        $this->view->setVar("colaborador_responsavel",$resultDesafio->usuarioResponsavelId);
        $this->view->setVar("colaboradores",  substr($idsParticipantes,0, strlen($idsParticipantes)-1));

    }
    
    public function modalAnalisarDesafioAction(){
        
        $this->disableLayoutBefore();

        $resultDesafio = Desafio::build()->findFirst($this->dispatcher->getParam('code'));
        
        $nomeParticipantes = $colaborador_responsavel = "";
        foreach ($resultDesafio->desafioUsuario as $participantes){

            $nomeParticipantes .= $participantes->usuario->nome.', ';
            
            if($resultDesafio->usuarioResponsavelId == $participantes->usuario->id){
                $colaborador_responsavel = $participantes->usuario->nome;
            }
        }
        
        $this->view->setVar("id",       $resultDesafio->id);
        $this->view->setVar("desafio",  $resultDesafio->desafio);
        $this->view->setVar("pontuacao",$resultDesafio->pontuacao);
        $this->view->setVar("tipo",     ($resultDesafio->desafio == Desafio::DESAFIO_TIPO_INDIVIDUAL)?'Individual':'Por Equipe');
        $this->view->setVar("inicioDt", (!empty($resultDesafio->inicioDt))?date('d/m/Y',strtotime($resultDesafio->inicioDt)):'');
        $this->view->setVar("fimDt",    (!empty($resultDesafio->fimDt))?date('d/m/Y',strtotime($resultDesafio->fimDt)):'');
        $this->view->setVar("premiacao",$resultDesafio->premiacao);
        $this->view->setVar("colaborador_responsavel",$colaborador_responsavel);
        $this->view->setVar("colaboradores",  substr($nomeParticipantes,0, strlen($nomeParticipantes)-2));

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
    
    public function analisarDesafioAction(){
        $this->view->disable();
        
        $auth = $this->auth->getIdentity(); 
        
        $dadosAnalise = $this->request->getPost('dados');

        $dados = new \stdClass();
        $dados->id          = $dadosAnalise['id'];
        $dados->empresaId   = $auth['empresaId'];
        $dados->observacao  = $dadosAnalise['observacao-analise'];
        $dados->resposta    = $this->request->getPost("resposta");

        $result = DesafioUsuario::build()->aprovarReprovarDesafio($dados);

        if($result['status'] == 'ok')
        {
            $this->flashSession->success($result['message']);
        }else{
            $this->flashSession->error($result['message']);
        }

        $this->response->redirect('empresa');
    }

}