<?php

namespace Colaborador\Controllers;

use Phalcon\Http\Response;
use Incentiv\Models\Ajuda;

class AjudaController extends ControllerBase
{
    public function initialize()
    {
        if (!$this->request->isAjax()) {
            $this->view->setTemplateBefore('private-colaborador');
        }
    }
    
    public function indexAction(){}
    
   public function modalAjudasAction(){
        $this->disableLayoutBefore();

        $resultAjudas  = Ajuda::build()->find(array("ajudaId IS NULL AND ativo = 'Y' ", "order" => "id"));

        $auth                   = $this->auth->getIdentity();
        $this->view->ajudas     = $resultAjudas;
        $this->view->usuarioId  = $auth['id'];
    } 
    
    public function pedirAjudaAction(){
        $this->disableLayoutBefore();
        
        $objAjuda = new \stdClass();
        $objAjuda->id       = $this->request->getPost("id");
        $objAjuda->mensagem = $this->request->getPost("ajuda");

        $resultAjuda = Ajuda::build()->pedirAjuda($objAjuda);

        if($resultAjuda['status'] == 'ok')
        {
            $this->flashSession->success($resultAjuda['message']);
        }else{
            $this->flashSession->error($resultAjuda['message']);
        }

        $this->response = new Response();
        $this->response->setJsonContent($resultAjuda['status'],'utf8');
        $this->response->send();

    }
    public function excluirAjudaAction(){
        $this->disableLayoutBefore();
        
        $objAjuda           = new \stdClass();
        $objAjuda->ajudaId  = $this->request->getPost("ajudaId");

        $resultAjuda = Ajuda::build()->excluirAjuda($objAjuda);

        if($resultAjuda['status'] == 'ok')
        {
            $this->flashSession->success($resultAjuda['message']);
        }else{
            $this->flashSession->error($resultAjuda['message']);
        }

        $this->response = new Response();
        $this->response->setJsonContent($resultAjuda['status'],'utf8');
        $this->response->send();

    }
}