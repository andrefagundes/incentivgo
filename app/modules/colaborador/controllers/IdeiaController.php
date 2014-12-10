<?php

namespace Colaborador\Controllers;

use Incentiv\Models\DesafioUsuario;

class IdeiaController extends ControllerBase {

    public function initialize() {
        if (!$this->request->isAjax()) {
            $this->view->usuario_logado    = $this->auth->getName();
            $this->view->setTemplateBefore('private-colaborador');
        }
    }

    public function indexAction() {
        
    }

    public function ideiaAction() {
    }
    
    public function modalIdeiasAction(){
        $this->disableLayoutBefore();
        
        $auth = $this->auth->getIdentity();
        
        $objDesafio = new \stdClass();
        $objDesafio->usuarioId = $auth['id'];
        
        $resultDesafiosUsuario  = DesafioUsuario::build()->buscarDesafiosUsuario($objDesafio);

        $this->view->desafios   = $resultDesafiosUsuario;
    } 

}