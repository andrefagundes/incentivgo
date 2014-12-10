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

        $resultAjudas  = Ajuda::build()->find(array("ajudaId IS NULL AND ativo = 'Y' ", "order" => "id"))->toArray();

        foreach ($resultAjudas as $key => $result){
          $resultAjudas[$key]['count_respostas'] =  Ajuda::count("ajudaId = ".$result['id']);
        }

        $auth                   = $this->auth->getIdentity();
        $this->view->ajudas     = $resultAjudas;
        $this->view->usuarioId  = $auth['id'];
    } 
    
    public function pedirAjudaAction(){
        $this->disableLayoutBefore();
        
        $auth = $this->auth->getIdentity();
        
        $objAjuda = new \stdClass();
        $objAjuda->id           = $this->request->getPost("id");
        $objAjuda->usuarioId    = $auth['id'];
        $objAjuda->mensagem     = $this->request->getPost("ajuda");

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
    
    public function modalAjudarAction(){
        $this->disableLayoutBefore();
        
        $dados = new \stdClass();
        $dados->ajudaId  = $this->dispatcher->getParam('ajudaId');

        $resultAjudas  = Ajuda::build()->find(array("(id = {$dados->ajudaId} OR ajudaId = {$dados->ajudaId}) AND  ativo = 'Y' ", "order" => "id"));

        $auth                   = $this->auth->getIdentity();
 
        $this->view->ajudas     = $resultAjudas;
        $this->view->usuarioId  = $auth['id'];
        $this->view->ajudaId    = $dados->ajudaId;
    } 
    
    public function ajudarAction(){
        $this->disableLayoutBefore();
        
        $auth = $this->auth->getIdentity();
        
        $objAjuda = new \stdClass();
        $objAjuda->usuarioId    = $auth['id'];
        $objAjuda->ajudaId      = $this->request->getPost("ajudaId");
        $objAjuda->mensagem     = $this->request->getPost("ajuda");

        $resultAjuda = Ajuda::build()->ajudar($objAjuda);

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