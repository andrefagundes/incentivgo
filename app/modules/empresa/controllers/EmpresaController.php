<?php

namespace Empresa\Controllers;

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

    public function indexAction() {
    }
}