<?php

namespace Colaborador\Controllers;

use \Incentiv\Models\Usuario;

class ChatController extends ControllerBase
{
    private $_lang = array();
    
    public function initialize()
    {
        if (!$this->request->isAjax()) {
            $this->view->setTemplateBefore('private-colaborador');
        }
        $this->_lang = parent::initialize();
    }
    
    public function indexAction(){}
    
    public function chatAction(){
        $this->disableLayoutBefore();
        $auth = $this->auth->getIdentity();
        $this->view->usuario_id        = $auth['id'];
        $this->view->usuarios          = Usuario::build()->find(array("empresaId = ".$auth['empresaId']." AND ativo = 'Y'",'columns' =>'id,empresaId,nome,email,cargo,avatar'))->toArray();     
    }
}