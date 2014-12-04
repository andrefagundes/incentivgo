<?php

namespace Colaborador\Controllers;

use Incentiv\Models\DesafioUsuario,
    Incentiv\Models\Ajuda;

class ColaboradorController extends ControllerBase
{
    public function initialize()
    {
        if (!$this->request->isAjax()) {
            $auth = $this->auth->getIdentity();
            $this->view->count_desafios = DesafioUsuario::build()->count("usuarioId = ".$auth['id']." AND ( usuarioResposta IS NULL OR usuarioResposta != 'N' ) AND envioAprovacaoDt IS NULL");
            $this->view->count_ajudas   = Ajuda::count("ativo = 'Y' AND ajudaId IS NULL");
            $this->view->setTemplateBefore('private-colaborador');
        }
    }
    
    public function indexAction(){}
}