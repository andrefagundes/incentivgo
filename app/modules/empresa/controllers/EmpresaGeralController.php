<?php

namespace Empresa\Controllers;

use Incentiv\Models\Desafio,
    Incentiv\Models\Usuario,
    Incentiv\Models\Ideia,
    Incentiv\Models\Perfil,
    Incentiv\Models\UsuarioPedidoRecompensa,
    Incentiv\Models\UsuarioPontuacaoCredito as Credito;

/**
 * Empresa\Controllers\EmpresaGeralController
 * CRUD para gerenciar empresas
 */
class EmpresaGeralController extends ControllerBase {
    
    private $_auth;

    public function initialize() {
        $this->_auth = $this->auth->getIdentity();
        $this->view->perfilAdmin     = Perfil::ADMINISTRADOR;
        $this->view->perfilId        = $this->_auth['perfilId'];
        parent::initialize();
        if (!$this->request->isAjax()) {
            
            if($this->_auth['perfilId'] == \Incentiv\Models\Perfil::GERENTE){
                $this->view->count_desafios    = Desafio::build()->count("empresaId = {$this->_auth['empresaId']} AND usuarioId = {$this->_auth['id']}");
            }else{
                $this->view->count_desafios    = Desafio::build()->count("empresaId = ".$this->_auth['empresaId']);
            }
            
            $this->view->count_usuarios            = Usuario::build()->count("empresaId = ".$this->_auth['empresaId']);
            $this->view->count_pedidos_incentiv    = UsuarioPedidoRecompensa::build()->count("empresaId = {$this->_auth['empresaId']} AND 
                                                        status = ".UsuarioPedidoRecompensa::PEDIDO_RECOMPENSA_ENVIADO);
            $this->view->count_pontuacao_total_gerada   = Credito::sum(array(
                                                            "column"     => "pontuacao",
                                                            "conditions" => "empresaId = {$this->_auth['empresaId']}"
                                                            ));
                                                
            $this->view->count_ideias_aprovacao   = Ideia::build()->count("empresaId = {$this->_auth['empresaId']} 
                AND status = 'Y' AND resposta IS NULL");
                                           
            $this->view->usuario_logado    = $this->auth->getName();
            $this->view->id                = $this->_auth['id'];
            $this->view->empresaId         = $this->_auth['empresaId'];
            $this->view->avatar            = $this->_auth['avatar'];
            $this->view->setTemplateAfter('private-empresa');
        }
    }

    public function indexAction() {
        $objDadosDesafioCumprido            = new \stdClass();
        $objDadosDesafioCumprido->empresaId = $this->_auth['empresaId'];
        $resultDesafiosCumpridos   = Desafio::build()->buscarDesafiosCumpridos($objDadosDesafioCumprido);

        $this->view->count_desafios_cumpridos   = count($resultDesafiosCumpridos);
        $this->view->desafios_cumpridos         = $resultDesafiosCumpridos;
    }
}