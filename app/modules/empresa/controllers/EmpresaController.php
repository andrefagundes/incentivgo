<?php

namespace Empresa\Controllers;

use Incentiv\Models\Desafio,
    Incentiv\Models\Usuario,
    Incentiv\Models\UsuarioPontuacaoCredito as Credito;

/**
 * Empresa\Controllers\EmpresaController
 * CRUD para gerenciar empresas
 */
class EmpresaController extends ControllerBase {
    
    private $_auth;

    public function initialize() {
        $this->_auth = $this->auth->getIdentity();
        if (!$this->request->isAjax()) {
            $this->view->count_desafios    = Desafio::build()->count("empresaId = ".$this->_auth['empresaId']);
            $this->view->count_usuarios    = Usuario::build()->count("empresaId = ".$this->_auth['empresaId']);
            $this->view->count_pontuacao   = Credito::sum(array(
                                                "column"     => "pontuacao",
                                                "conditions" => "empresaId = {$this->_auth['empresaId']}"
                                            ));
            $this->view->usuario_logado    = $this->auth->getName();
            $this->view->id                = $this->_auth['id'];
            $this->view->empresaId         = $this->_auth['empresaId'];
            $this->view->avatar            = $this->_auth['avatar'];
            $this->view->setTemplateBefore('private-empresa');
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