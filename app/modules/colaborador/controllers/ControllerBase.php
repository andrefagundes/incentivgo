<?php
namespace Colaborador\Controllers;

use Phalcon\Mvc\Controller,
    Phalcon\Mvc\View;

/**
 * ControllerBase
 * Este é o controlador de base para todos os controladores na aplicação
 */
class ControllerBase extends Controller
{
    //multi linguas(services.php)
    public function initialize(){
        $this->view->lang = $this->getDI()->getShared('lang');
    }
    
    public function disableLayoutBefore(){
        return $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }
}