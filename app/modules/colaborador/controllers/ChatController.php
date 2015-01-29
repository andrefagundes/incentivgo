<?php

namespace Colaborador\Controllers;

use \Incentiv\Models\Usuario;

class ChatController extends ControllerBase
{
    public function initialize()
    {
        if (!$this->request->isAjax()) {
            $this->view->setTemplateBefore('private-colaborador');
        }
    }
    
    public function indexAction(){}
    
    public function chatAction(){
        $this->disableLayoutBefore();
        $auth = $this->auth->getIdentity();
        $this->view->usuario_id        = $auth['id'];
        $this->view->usuarios          = Usuario::build()->find(array("empresaId = ".$auth['empresaId']." AND ativo = 'Y'",'columns' =>'id,nome,email,avatar'))->toArray();     
    }
}