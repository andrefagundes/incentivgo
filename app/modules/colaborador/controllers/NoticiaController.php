<?php

namespace Colaborador\Controllers;

use Incentiv\Models\Noticia;

class NoticiaController extends ControllerBase {
    
    private $_lang = array();


    public function initialize() {
        if (!$this->request->isAjax()) {
            $auth = $this->auth->getIdentity();

            $this->view->usuario_logado = $this->auth->getName();
            $this->view->avatar = $auth['avatar'];
            $this->view->empresaId         = $auth['empresaId'];
            $this->view->id                = $auth['id'];
            $this->view->setTemplateBefore('private-colaborador');
        }
        $this->_lang = parent::initialize();
    }

    public function indexAction() {}

    public function noticiaAction() {
        $resultNoticias = Noticia::build()->find(array("order" => "id"));
        $this->view->noticias = $resultNoticias;
    }

    public function modalNoticiasAction() {
        $this->disableLayoutBefore();

        $resultNoticias = Noticia::build()->find(array("order" => "id"));
        $this->view->noticias = $resultNoticias;
    }
    
    public function modalLerNoticiaAction() {
        $this->disableLayoutBefore();
        
        $id = $this->dispatcher->getParam("code");
        $resultNoticia = Noticia::build()->findFirst("id = {$id}");

        $this->view->noticia = $resultNoticia;
    }

}