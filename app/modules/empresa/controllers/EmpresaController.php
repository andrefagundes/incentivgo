<?php

namespace Empresa\Controllers;

/**
 * Empresa\Controllers\EmpresaController
 * CRUD para gerenciar empresas
 */
class EmpresaController extends ControllerBase {

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

    public function indexAction() {
    }
}