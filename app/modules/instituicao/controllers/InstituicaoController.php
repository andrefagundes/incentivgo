<?php

namespace Instituicao\Controllers;

/**
 * Instituicao\Controllers\InstituicaoController
 * CRUD para gerenciar usuários
 */
class InstituicaoController extends ControllerBase
{

    public function initialize()
    {
        $this->view->setTemplateBefore('private_instituicao');
    }

    /**
     * Action padrão, mostra o formulário de busca
     */
    public function indexAction()
    {

    }
}