<?php

namespace Colaborador\Controllers;

use Phalcon\Http\Response;
use Incentiv\Models\DesafioUsuario;

class DesafioController extends ControllerBase
{
    private $_auth;
    
    public function initialize()
    {
        $this->_auth = $this->auth->getIdentity();
        if (!$this->request->isAjax()) {
            $this->view->setTemplateBefore('private-colaborador');
        }
    }
    
    public function indexAction(){}
    
    public function modalDesafiosAction(){
        $this->disableLayoutBefore();
        
        $objDesafio = new \stdClass();
        $objDesafio->usuarioId = $this->_auth['id'];
        
        $resultDesafiosUsuario  = DesafioUsuario::build()->buscarDesafiosUsuario($objDesafio);

        $this->view->desafios   = $resultDesafiosUsuario;
    } 
    
    public function responderDesafioAction(){
        $this->disableLayoutBefore();
        
        $objDesafio = new \stdClass();
        $objDesafio->id         = $this->request->getPost("id");
        $objDesafio->resposta   = $this->request->getPost("resposta");
        $objDesafio->motivo     = $this->request->getPost("motivo");
        
        $resultDesafiosUsuario  = DesafioUsuario::build()->responderDesafioUsuario($objDesafio);
        
        if($resultDesafiosUsuario['status'] == 'ok')
        {
            $this->flashSession->success($resultDesafiosUsuario['message']);
        }else{
            $this->flashSession->error($resultDesafiosUsuario['message']);
        }
        
        $this->response = new Response();
        $this->response->setJsonContent($resultDesafiosUsuario['status'],'utf8');
        $this->response->send();
    }
    
    public function desafioCumpridoAction(){
        $this->disableLayoutBefore();
        
        $objDesafio = new \stdClass();
        $objDesafio->id         = $this->request->getPost("id");
        
        $resultDesafiosUsuario  = DesafioUsuario::build()->desafioCumpridoUsuario($objDesafio);
        
        if($resultDesafiosUsuario['status'] == 'ok')
        {
            $this->flashSession->success($resultDesafiosUsuario['message']);
        }else{
            $this->flashSession->error($resultDesafiosUsuario['message']);
        }
        
        $this->response = new Response();
        $this->response->setJsonContent($resultDesafiosUsuario['status'],'utf8');
        $this->response->send();
    }
}