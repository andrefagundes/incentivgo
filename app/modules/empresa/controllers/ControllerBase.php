<?php
namespace Empresa\Controllers;

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
        $lang = $this->getDI()->getShared('lang');
        $this->view->lang = $lang;
        return $lang;
    }
    
    public function disableLayoutBefore(){
        return $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }
}