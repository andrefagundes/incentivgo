<?php

namespace Colaborador\Controllers;

use Incentiv\Models\Desafio,
    Incentiv\Models\Ajuda;

class ColaboradorController extends ControllerBase
{
    public function initialize()
    {
        if (!$this->request->isAjax()) {
            $this->view->count_desafios = Desafio::count("ativo = 'Y'");
            $this->view->count_ajudas   = Ajuda::count("ativo = 'Y' AND ajudaId IS NULL");
            $this->view->setTemplateBefore('private-colaborador');
        }
    }
    
    public function indexAction(){}
}