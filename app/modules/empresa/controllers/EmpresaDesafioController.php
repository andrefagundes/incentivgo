<?php

namespace Empresa\Controllers;

use Phalcon\Paginator\Adapter\Model as Paginator,
    Phalcon\Http\Response;
use Incentiv\Models\Usuario,
    Incentiv\Models\Desafio,
    Incentiv\Models\DesafioPontuacao,
    Incentiv\Models\DesafioUsuario,
    Incentiv\Models\DesafioTipo,
    Incentiv\Models\Perfil;

/**
 * Empresa\Controllers\Empresa_DesafioController
 * Classe para gerenciar desafios
 */
class EmpresaDesafioController extends ControllerBase {
    private $_auth;
    private $_lang = array();
    
    public function initialize() {
        $this->_auth = $this->auth->getIdentity();
        $this->view->perfilAdmin     = Perfil::ADMINISTRADOR;
        $this->view->perfilId        = $this->_auth['perfilId'];
        if (!$this->request->isAjax()) {        
            $this->view->usuario_logado    = $this->auth->getName();
            $this->view->avatar            = $this->_auth['avatar'];
            $this->view->id                = $this->_auth['id'];
            $this->view->empresaId         = $this->_auth['empresaId'];
            $this->view->setTemplateAfter('private-empresa');
        }
       $this->_lang = parent::initialize();
    }
    /**
     * Action padrão, mostra o formulário de busca
     */
    public function indexAction() {
        
    }
    
    public function desafioAction() { 
       $this->view->perfilGerente   = Perfil::GERENTE;
       $this->view->perfilAdmin     = Perfil::ADMINISTRADOR;
       $this->view->perfilId        = $this->_auth['perfilId'];
    }
    
    public function pesquisarDesafioAction() {

        $this->disableLayoutBefore();
        
        $objDesafio = new \stdClass();
        $objDesafio->usuarioId  = $this->_auth['id'];
        $objDesafio->empresaId  = $this->_auth['empresaId'];
        $objDesafio->perfilId   = $this->_auth['perfilId'];
        $objDesafio->ativo      = $this->request->getPost("ativo");
        $objDesafio->filter     = $this->request->getPost("filter");

        $resultRegrasDesafios = Desafio::build()->fetchAllDesafios($objDesafio);
        
        $numberPage = $this->request->getPost("page");
        $paginator = new Paginator(array(
            "data" => $resultRegrasDesafios,
            "limit" => 4,
            "page" => $numberPage
        ));
        
        $this->view->usuarioId = $this->_auth['id'];
        $this->view->page = $paginator->getPaginate();
        $this->view->pick("empresa_desafio/pesquisar-desafio");
    }
    
    public function modalDesafioAction(){
        
        $this->disableLayoutBefore();

        $resultDesafio = Desafio::build()->findFirst($this->dispatcher->getParam('code'));
        
        $arrayParticipantes = $arrayUsuarioResponsavelId = array();
        $idsParticipantes = $idUsuarioResponsavel = array();
        foreach ($resultDesafio->desafioUsuario as $participantes){
            $arrayParticipantes[$participantes->usuarioId] = utf8_decode($participantes->usuario->nome);
            $idsParticipantes[] = $participantes->usuarioId;
        }
//        die(var_dump($arrayParticipantes));
        if($resultDesafio->tipo == Desafio::DESAFIO_TIPO_EQUIPE){
                $usuarioResp = Usuario::build()->findFirst($resultDesafio->usuarioResponsavelId);
                $arrayUsuarioResponsavelId[$usuarioResp->id] = $usuarioResp->nome;
                $idUsuarioResponsavel = $usuarioResp->id;
        }

        $desafios_tipo = DesafioTipo::build()->buscarTiposDesafio($this->_auth['empresaId']);

        foreach ($desafios_tipo as $desafios) {
            $desafioTipo = ($this->_lang['lang'] == 'pt-BR')?$desafios->desafioTipo:$desafios->desafioTipoEn;
            $nivel_desafios[$desafios->id] = '<strong>'.$desafioTipo.'</strong> ( '.$desafios->pontuacao." incentivs )"; 
        }

        $dt_inicio = $dt_fim = '';
        if($resultDesafio->inicioDt){
            $dt_inicio = $this->di->get('funcoes')->formatarDataSaida($resultDesafio->inicioDt,$this->_lang['lang']);
        }
        if($resultDesafio->fimDt){
            $dt_fim = $this->di->get('funcoes')->formatarDataSaida($resultDesafio->fimDt,$this->_lang['lang']);
        }

        $this->view->setVar('nivel_desafios',$nivel_desafios);

        $this->view->setVar("id",       $resultDesafio->id);
        $this->view->setVar("desafio",  $resultDesafio->desafio);
        $this->view->setVar("desafioTipoId",$resultDesafio->desafioTipoId);
        $this->view->setVar("tipo",     $resultDesafio->tipo);
        $this->view->setVar("inicioDt", $dt_inicio);
        $this->view->setVar("fimDt",    $dt_fim);
        $this->view->setVar("premiacao",$resultDesafio->premiacao);
        $this->view->setVar("colaborador_responsavel",$arrayUsuarioResponsavelId);
        $this->view->setVar("id_colaborador_responsavel",$idUsuarioResponsavel);
        $this->view->setVar("colaboradores",  $arrayParticipantes);
//        die(substr($idsParticipantes,0, strlen($idsParticipantes)-2));
        $this->view->setVar("arr_colaboradores_participantes", $idsParticipantes);

    }
    
    public function modalAnalisarDesafioAction(){
        
        $this->disableLayoutBefore();

        $resultDesafio    = Desafio::build()->findFirst($this->dispatcher->getParam('code'));
        $pontuacaoDesafio = DesafioPontuacao::build()->findFirst(array("empresaId = {$this->_auth['empresaId']} AND 
                            {$resultDesafio->desafioTipoId} AND ativo = 'Y'",'columns' => 'pontuacao'));
        
        $nomeParticipantes = $colaborador_responsavel = "";
        foreach ($resultDesafio->desafioUsuario as $participantes){

            $nomeParticipantes .= $participantes->usuario->nome.', ';
            
            if($resultDesafio->usuarioResponsavelId == $participantes->usuario->id){
                $colaborador_responsavel = $participantes->usuario->nome;
            }
        }
        
        $dt_inicio = $dt_fim = '';
        if($resultDesafio->inicioDt){
            $dt_inicio = $this->di->get('funcoes')->formatarDataSaida($resultDesafio->inicioDt,$this->_lang['lang']);
        }
        if($resultDesafio->fimDt){
            $dt_fim = $this->di->get('funcoes')->formatarDataSaida($resultDesafio->fimDt,$this->_lang['lang']);
        }

        $this->view->setVar("id",       $resultDesafio->id);
        $this->view->setVar("desafio",  $resultDesafio->desafio);
        $this->view->setVar("pontuacao",$pontuacaoDesafio->pontuacao);
        $this->view->setVar("desafioTipoId",$resultDesafio->desafioTipoId);
        $this->view->setVar("tipo",     ($resultDesafio->desafio == Desafio::DESAFIO_TIPO_INDIVIDUAL)?'Individual':'Por Equipe');
        $this->view->setVar("inicioDt", $dt_inicio);
        $this->view->setVar("fimDt", $dt_fim);
        $this->view->setVar("premiacao",$resultDesafio->premiacao);
        $this->view->setVar("colaborador_responsavel",$colaborador_responsavel);
        $this->view->setVar("colaboradores",  substr($nomeParticipantes,0, strlen($nomeParticipantes)-2));

    }
    
    public function pesquisarColaboradoresDesafioAction() {

        $this->disableLayoutBefore();
        
        $objUsuario = new \stdClass();
        $objUsuario->filter         = $this->request->get("filter");
        $objUsuario->colaboradores  = $this->request->get("colaboradores");
        $objUsuario->usuarioLogado  = $this->_auth['id'];
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
            $dados['usuarioId'] = $this->_auth['id'];
            $dados['empresaId'] = $this->_auth['empresaId'];

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
        
        $dadosAnalise = $this->request->getPost('dados');

        $dados = new \stdClass();
        $dados->id          = $dadosAnalise['id'];
        $dados->empresaId   = $this->_auth['empresaId'];
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
    
    public function mapearPontuacaoAction(){
        $this->disableLayoutBefore();
        
        if ($this->request->isPost()) {
            
            $objMapeamento = new \stdClass();
            $objMapeamento->dados               = $this->request->getPost('dados');
            $objMapeamento->dados['empresaId']  = $this->_auth['empresaId'];

            $result = DesafioPontuacao::build()->salvarMapeamentoDesafio($objMapeamento);
            
            if ($result['status'] == 'ok') {
                $this->flashSession->success($result['message']);
            } else {
                $this->flashSession->error($result['message']);
            }
            
            $this->response->redirect('empresa/desafio');
        }

        $resultTiposDesafio  = DesafioTipo::build()->buscarTiposDesafio($this->_auth['empresaId']);
//die(var_dump($resultTiposDesafio->toArray()));
        $this->view->setVar("tipo_desafios",$resultTiposDesafio);

    }

}