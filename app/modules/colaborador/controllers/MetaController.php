<?php

namespace Colaborador\Controllers;

use Phalcon\Http\Response;
use \Incentiv\Models\Meta;

class MetaController extends ControllerBase {

    public function initialize() {
        if (!$this->request->isAjax()) {
            $this->view->usuario_logado    = $this->auth->getName();
            $this->view->setTemplateBefore('private-colaborador');
        }
    }

    public function indexAction() {
        
    }

    public function metaAction() {
        
        $auth  = $this->auth->getIdentity();
    
        $tipoMeta = $this->dispatcher->getParam('tipo');

        $resultMetas  = Meta::build()->find(array("usuarioId = {$auth['id']} AND tipo = {$tipoMeta} AND  ativo = 'Y' ", "order" => "id"));
        
        $this->view->tipoMeta    = $tipoMeta;
        $this->view->resultMetas = $resultMetas;
    }
    
    public function salvarMetaAction(){
        $this->disableLayoutBefore();
        
        $auth = $this->auth->getIdentity();
  
        $objMeta = new \stdClass();
        $objMeta->id           = $this->request->getPost("id");
        $objMeta->descricao    = $this->request->getPost("descricao");
        $objMeta->usuarioId    = $auth['id'];
        $objMeta->empresaId    = $auth['empresaId'];

        $resultMeta = Meta::build()->salvarMeta($objMeta);

        if($resultMeta['status'] == 'ok')
        {
            $this->flashSession->success($resultMeta['message']);
        }else{
            $this->flashSession->error($resultMeta['message']);
        }

        $this->response = new Response();
        $this->response->setJsonContent($resultMeta['status'],'utf8');
        $this->response->send();

    }

}